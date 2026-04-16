<?php

namespace App\Services\AcademicPlanning\Dashboard;

use Illuminate\Support\Facades\DB;

class ProgressComparisonService
{
    public function calculate(int $studentId, ?int $studyPlanVersionId = null): array
    {
        $versionId = $studyPlanVersionId ?? $this->resolveLatestVersionId($studentId);

        $plannedBySemester = DB::table('study_plan_items')
            ->join('study_plan_semesters', 'study_plan_semesters.id', '=', 'study_plan_items.study_plan_semester_id')
            ->where('study_plan_items.study_plan_version_id', $versionId)
            ->selectRaw('study_plan_semesters.semester_id, SUM(study_plan_items.credits_snapshot) AS planned_credits')
            ->groupBy('study_plan_semesters.semester_id')
            ->orderBy('study_plan_semesters.semester_id')
            ->get();

        $actualBySemester = DB::table('course_results')
            ->join('courses', 'courses.id', '=', 'course_results.course_id')
            ->where('course_results.student_id', $studentId)
            ->whereIn('course_results.result', ['A', 'B', 'C', 'D', 'P'])
            ->selectRaw('course_results.semester_id, SUM(courses.credits) AS actual_credits')
            ->groupBy('course_results.semester_id')
            ->orderBy('course_results.semester_id')
            ->get();

        return [
            'student_id' => $studentId,
            'study_plan_version_id' => $versionId,
            'planned_series' => $plannedBySemester,
            'actual_series' => $actualBySemester,
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
