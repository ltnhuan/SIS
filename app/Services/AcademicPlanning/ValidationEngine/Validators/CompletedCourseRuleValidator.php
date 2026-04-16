<?php

namespace App\Services\AcademicPlanning\ValidationEngine\Validators;

use App\Enums\AcademicPlanning\Validation\ValidationSeverity;
use App\Services\AcademicPlanning\ValidationEngine\Contracts\RuleValidatorInterface;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationIssue;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;
use Illuminate\Support\Facades\DB;

class CompletedCourseRuleValidator implements RuleValidatorInterface
{
    public function validate(ValidationContext $context, ValidationResult $result): void
    {
        $alreadyPassed = DB::table('course_results')
            ->where('student_id', $context->studentId)
            ->where('course_id', $context->courseId)
            ->whereIn('result', ['A', 'B', 'C', 'D', 'P'])
            ->exists();

        if (! $alreadyPassed) {
            return;
        }

        $result->addIssue(new ValidationIssue(
            code: 'course.already_completed',
            severity: ValidationSeverity::BLOCKING->value,
            message: 'Học phần đã hoàn thành, không thể thêm lại vào kế hoạch.',
            meta: ['course_id' => $context->courseId],
        ));
    }
}
