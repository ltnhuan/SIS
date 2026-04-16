<?php

namespace App\Services\AcademicPlanning\ValidationEngine\Validators;

use App\Enums\AcademicPlanning\Validation\ValidationSeverity;
use App\Services\AcademicPlanning\ValidationEngine\Contracts\RuleValidatorInterface;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationIssue;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;
use Illuminate\Support\Facades\DB;

class InProgressCourseRuleValidator implements RuleValidatorInterface
{
    public function validate(ValidationContext $context, ValidationResult $result): void
    {
        $isInProgress = DB::table('enrollments')
            ->where('student_id', $context->studentId)
            ->where('course_id', $context->courseId)
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->exists();

        if (! $isInProgress) {
            return;
        }

        $result->addIssue(new ValidationIssue(
            code: 'course.currently_in_progress',
            severity: ValidationSeverity::BLOCKING->value,
            message: 'Học phần đang được học ở kỳ hiện tại, không thể thêm trùng.',
            meta: ['course_id' => $context->courseId],
        ));
    }
}
