<?php

namespace App\Services\AcademicPlanning\Dashboard;

use Illuminate\Support\Facades\DB;

class SemesterWorkloadAnalyticsService
{
    public function calculate(int $studentId, ?int $studyPlanVersionId = null): array
    {
        $versionId = $studyPlanVersionId ?? $this->resolveLatestVersionId($studentId);

        $rows = DB::table('study_plan_items')
            ->join('study_plan_semesters', 'study_plan_semesters.id', '=', 'study_plan_items.study_plan_semester_id')
            ->join('courses', 'courses.id', '=', 'study_plan_items.course_id')
            ->where('study_plan_items.study_plan_version_id', $versionId)
            ->selectRaw('study_plan_semesters.semester_id, SUM(study_plan_items.credits_snapshot) AS total_credits, COUNT(study_plan_items.id) AS total_courses')
            ->groupBy('study_plan_semesters.semester_id')
            ->orderBy('study_plan_semesters.semester_id')
            ->get();

        $max = (int) config('academic_planning.max_credits_per_semester', 24);

        $series = $rows->map(function ($row) use ($max): array {
            $risk = 'green';
            if ((int) $row->total_credits > $max) {
                $risk = 'red';
            } elseif ((int) $row->total_credits >= $max - 3) {
                $risk = 'yellow';
            }

            return [
                'semester_id' => (int) $row->semester_id,
                'total_credits' => (int) $row->total_credits,
                'total_courses' => (int) $row->total_courses,
                'estimated_weekly_hours' => (int) $row->total_credits * 3,
                'risk_color' => $risk,
            ];
        })->values()->all();

        return [
            'student_id' => $studentId,
            'study_plan_version_id' => $versionId,
            'max_credits_per_semester' => $max,
            'series' => $series,
        ];
    }

    private function resolveLatestVersionId(int $studentId): ?int
    {
        return DB::table('study_plan_versions')
            ->join('study_plans', 'study_plans.id', '=', 'study_plan_versions.study_plan_id')
            ->where('study_plans.student_id', $studentId)
            ->orderByDesc('study_plan_versions.id')
            ->value('study_plan_versions.id');
    }
}
