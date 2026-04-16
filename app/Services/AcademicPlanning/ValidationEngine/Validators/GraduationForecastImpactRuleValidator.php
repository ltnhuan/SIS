<?php

namespace App\Services\AcademicPlanning\ValidationEngine\Validators;

use App\Enums\AcademicPlanning\Validation\ValidationSeverity;
use App\Services\AcademicPlanning\ValidationEngine\Contracts\RuleValidatorInterface;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationIssue;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;
use Illuminate\Support\Facades\DB;

class GraduationForecastImpactRuleValidator implements RuleValidatorInterface
{
    public function validate(ValidationContext $context, ValidationResult $result): void
    {
        $curriculumCredits = (int) DB::table('curricula')->where('id', $context->version->studyPlan->curriculum_id)->value('total_credits');
        if ($curriculumCredits <= 0) {
            return;
        }

        $passedCredits = (int) DB::table('course_results')
            ->join('courses', 'courses.id', '=', 'course_results.course_id')
            ->where('course_results.student_id', $context->studentId)
            ->whereIn('course_results.result', ['A', 'B', 'C', 'D', 'P'])
            ->sum('courses.credits');

        $plannedCredits = (int) DB::table('study_plan_items')
            ->where('study_plan_version_id', $context->version->id)
            ->sum('credits_snapshot');

        $remainingCreditsAfterAdd = max(0, $curriculumCredits - ($passedCredits + $plannedCredits + $context->courseCredits));
        $remainingSemesters = (int) DB::table('semesters')->where('start_date', '>=', now()->toDateString())->count();
        $remainingSemesters = max(1, $remainingSemesters);
        $requiredCreditsPerSemester = (int) ceil($remainingCreditsAfterAdd / $remainingSemesters);
        $maxCreditsPerSemester = (int) config('academic_planning.max_credits_per_semester', 24);

        if ($requiredCreditsPerSemester <= $maxCreditsPerSemester) {
            return;
        }

        $severity = $requiredCreditsPerSemester > ($maxCreditsPerSemester + 3)
            ? ValidationSeverity::BLOCKING->value
            : ValidationSeverity::WARNING->value;

        $result->addIssue(new ValidationIssue(
            code: 'graduation.forecast_risk',
            severity: $severity,
            message: 'Phân bổ tín chỉ còn lại có nguy cơ ảnh hưởng tiến độ tốt nghiệp.',
            meta: [
                'remaining_credits_after_add' => $remainingCreditsAfterAdd,
                'remaining_semesters' => $remainingSemesters,
                'required_credits_per_semester' => $requiredCreditsPerSemester,
                'max_credits_per_semester' => $maxCreditsPerSemester,
            ],
        ));
    }
}
