<?php

namespace App\Services\AcademicPlanning\ValidationEngine\Validators;

use App\Enums\AcademicPlanning\Validation\ValidationSeverity;
use App\Services\AcademicPlanning\PrerequisiteValidationService;
use App\Services\AcademicPlanning\ValidationEngine\Contracts\RuleValidatorInterface;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationIssue;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;

class PrerequisiteSatisfiedRuleValidator implements RuleValidatorInterface
{
    public function __construct(private readonly PrerequisiteValidationService $prerequisiteValidationService)
    {
    }

    public function validate(ValidationContext $context, ValidationResult $result): void
    {
        $prereq = $this->prerequisiteValidationService->validateCourseForStudent($context->studentId, $context->courseId);

        if ($prereq['passed']) {
            return;
        }

        $result->addIssue(new ValidationIssue(
            code: 'prerequisite.not_satisfied',
            severity: ValidationSeverity::BLOCKING->value,
            message: 'Chưa đáp ứng điều kiện tiên quyết của học phần.',
            meta: ['missing_prerequisites' => $prereq['missing_prerequisites']],
        ));
    }
}
