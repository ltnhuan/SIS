<?php

namespace App\Services\AcademicPlanning\Dashboard;

use Illuminate\Support\Facades\DB;

class ProgressRiskDetectionService
{
    public function detect(int $studentId, ?int $studyPlanVersionId = null): array
    {
        $versionId = $studyPlanVersionId ?? $this->resolveLatestVersionId($studentId);

        $failedUnplannedCount = DB::table('course_results')
            ->where('student_id', $studentId)
            ->whereIn('result', ['F', 'FX'])
            ->whereNotIn('course_id', function ($query) use ($versionId): void {
                $query->from('study_plan_items')->select('course_id')->where('study_plan_version_id', $versionId);
            })
            ->count();

        $prereqViolationCount = DB::table('study_plan_items')
            ->where('study_plan_version_id', $versionId)
            ->where('is_prerequisite_satisfied', false)
            ->count();

        $overloadCount = DB::table('study_plan_items')
            ->where('study_plan_version_id', $versionId)
            ->where('is_credit_overload', true)
            ->count();

        $riskLevel = 'low';
        if ($failedUnplannedCount > 0 || $prereqViolationCount > 0) {
            $riskLevel = 'medium';
        }
        if ($failedUnplannedCount > 2 || $prereqViolationCount > 2 || $overloadCount > 0) {
            $riskLevel = 'high';
        }

        return [
            'student_id' => $studentId,
            'study_plan_version_id' => $versionId,
            'risk_level' => $riskLevel,
            'failed_unplanned_retake_count' => $failedUnplannedCount,
            'prerequisite_violation_count' => $prereqViolationCount,
            'credit_overload_count' => $overloadCount,
            'flags' => [
                'failed_unplanned_retake' => $failedUnplannedCount > 0,
                'prerequisite_violation' => $prereqViolationCount > 0,
                'credit_overload' => $overloadCount > 0,
            ],
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
