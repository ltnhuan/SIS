<?php

namespace App\Services\AcademicPlanning\Dashboard;

use Illuminate\Support\Facades\DB;

class GraduationForecastService
{
    public function calculate(int $studentId, ?int $studyPlanVersionId = null): array
    {
        $versionId = $studyPlanVersionId ?? $this->resolveLatestVersionId($studentId);
        $curriculumId = DB::table('study_plans')
            ->join('study_plan_versions', 'study_plan_versions.study_plan_id', '=', 'study_plans.id')
            ->where('study_plan_versions.id', $versionId)
            ->value('study_plans.curriculum_id');

        $curriculumTotalCredits = (int) DB::table('curricula')->where('id', $curriculumId)->value('total_credits');
        $passedCredits = (int) DB::table('course_results')
            ->join('courses', 'courses.id', '=', 'course_results.course_id')
            ->where('course_results.student_id', $studentId)
            ->whereIn('course_results.result', ['A', 'B', 'C', 'D', 'P'])
            ->sum('courses.credits');

        $plannedCredits = $versionId
            ? (int) DB::table('study_plan_items')->where('study_plan_version_id', $versionId)->sum('credits_snapshot')
            : 0;

        $remainingCredits = max(0, $curriculumTotalCredits - max($passedCredits, $plannedCredits));
        $maxCreditsPerSemester = (int) config('academic_planning.max_credits_per_semester', 24);
        $requiredSemesters = (int) ceil(max(1, $remainingCredits) / max(1, $maxCreditsPerSemester));

        $nextSemester = DB::table('semesters')
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->first();

        $estimatedGraduationDate = $nextSemester?->start_date
            ? now()->parse($nextSemester->start_date)->addMonths($requiredSemesters * 4)->toDateString()
            : now()->addMonths($requiredSemesters * 4)->toDateString();

        return [
            'student_id' => $studentId,
            'study_plan_version_id' => $versionId,
            'curriculum_total_credits' => $curriculumTotalCredits,
            'passed_credits' => $passedCredits,
            'planned_credits' => $plannedCredits,
            'remaining_credits' => $remainingCredits,
            'max_credits_per_semester' => $maxCreditsPerSemester,
            'estimated_required_semesters' => $requiredSemesters,
            'estimated_graduation_date' => $estimatedGraduationDate,
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
