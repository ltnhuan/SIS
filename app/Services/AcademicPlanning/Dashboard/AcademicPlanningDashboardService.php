<?php

namespace App\Services\AcademicPlanning\Dashboard;

use App\Jobs\AcademicPlanning\RecomputeAcademicPlanningDashboardSnapshotJob;
use App\Models\AcademicPlanningDashboardSnapshot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AcademicPlanningDashboardService
{
    public function __construct(
        private readonly GraduationForecastService $graduationForecastService,
        private readonly ProgressComparisonService $progressComparisonService,
        private readonly SemesterWorkloadAnalyticsService $semesterWorkloadAnalyticsService,
        private readonly ProgressRiskDetectionService $progressRiskDetectionService,
    ) {
    }

    public function getStudentDashboard(int $studentId, bool $forceRefresh = false): array
    {
        $cacheKey = "academic_planning_dashboard:student:{$studentId}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($studentId): array {
            $forecast = $this->graduationForecastService->calculate($studentId);
            $progress = $this->progressComparisonService->calculate($studentId, $forecast['study_plan_version_id']);
            $workload = $this->semesterWorkloadAnalyticsService->calculate($studentId, $forecast['study_plan_version_id']);
            $risk = $this->progressRiskDetectionService->detect($studentId, $forecast['study_plan_version_id']);

            AcademicPlanningDashboardSnapshot::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'snapshot_date' => Carbon::today()->toDateString(),
                ],
                [
                    'study_plan_version_id' => $forecast['study_plan_version_id'],
                    'forecast_payload' => $forecast,
                    'progress_payload' => $progress,
                    'workload_payload' => $workload,
                    'risk_payload' => $risk,
                    'calculated_at' => now(),
                ]
            );

            return [
                'forecast' => $forecast,
                'progress' => $progress,
                'workload' => $workload,
                'risk' => $risk,
            ];
        });
    }

    public function queueRecompute(int $studentId): void
    {
        RecomputeAcademicPlanningDashboardSnapshotJob::dispatch($studentId);
    }

    public function getAdvisorDashboard(int $advisorId): array
    {
        return [
            'advisor_id' => $advisorId,
            'students_at_risk' => AcademicPlanningDashboardSnapshot::query()
                ->whereDate('snapshot_date', Carbon::today())
                ->whereJsonContains('risk_payload->risk_level', 'high')
                ->count(),
            'pending_reviews' => \DB::table('study_plan_versions')
                ->join('study_plans', 'study_plans.id', '=', 'study_plan_versions.study_plan_id')
                ->where('study_plans.advisor_id', $advisorId)
                ->where('study_plan_versions.status', 'submitted')
                ->count(),
        ];
    }

    public function getRegistrarDashboard(): array
    {
        return [
            'approval_rate' => $this->calculateApprovalRate(),
            'high_risk_students' => AcademicPlanningDashboardSnapshot::query()
                ->whereDate('snapshot_date', Carbon::today())
                ->whereJsonContains('risk_payload->risk_level', 'high')
                ->count(),
            'forecast_demand_next_semester' => \DB::table('study_plan_items')
                ->join('study_plan_semesters', 'study_plan_semesters.id', '=', 'study_plan_items.study_plan_semester_id')
                ->selectRaw('study_plan_semesters.semester_id, study_plan_items.course_id, COUNT(*) AS demand_count')
                ->groupBy('study_plan_semesters.semester_id', 'study_plan_items.course_id')
                ->orderByDesc('demand_count')
                ->limit(20)
                ->get(),
        ];
    }

    private function calculateApprovalRate(): float
    {
        $total = \DB::table('study_plan_versions')->count();
        if ($total === 0) {
            return 0;
        }

        $approved = \DB::table('study_plan_versions')->where('status', 'approved')->count();

        return round(($approved / $total) * 100, 2);
    }
}
