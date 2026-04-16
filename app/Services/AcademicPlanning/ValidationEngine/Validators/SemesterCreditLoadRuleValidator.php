<?php

namespace App\Services\AcademicPlanning\ValidationEngine\Validators;

use App\Enums\AcademicPlanning\Validation\ValidationSeverity;
use App\Services\AcademicPlanning\ValidationEngine\Contracts\RuleValidatorInterface;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationIssue;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;
use Illuminate\Support\Facades\DB;

class SemesterCreditLoadRuleValidator implements RuleValidatorInterface
{
    public function validate(ValidationContext $context, ValidationResult $result): void
    {
        $plannedCredits = (int) DB::table('study_plan_semesters')
            ->where('study_plan_version_id', $context->version->id)
            ->where('semester_id', $context->semesterId)
            ->value('planned_credits');

        $maxCredits = (int) config('academic_planning.max_credits_per_semester', 24);
        $afterAdd = $plannedCredits + $context->courseCredits;

        if ($afterAdd <= $maxCredits) {
            if ($afterAdd >= $maxCredits - 3) {
                $result->addIssue(new ValidationIssue(
                    code: 'semester.credit_near_limit',
                    severity: ValidationSeverity::WARNING->value,
                    message: 'Tín chỉ học kỳ đã gần ngưỡng tối đa.',
                    meta: ['planned_credits_after_add' => $afterAdd, 'max_credits' => $maxCredits],
                ));
            }

            return;
        }

        $result->addIssue(new ValidationIssue(
            code: 'semester.credit_overload',
            severity: ValidationSeverity::BLOCKING->value,
            message: 'Tổng tín chỉ học kỳ vượt ngưỡng tối đa cho phép.',
            meta: ['planned_credits_after_add' => $afterAdd, 'max_credits' => $maxCredits],
        ));
    }
}
