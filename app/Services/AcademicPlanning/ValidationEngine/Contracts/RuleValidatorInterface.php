<?php

namespace App\Services\AcademicPlanning\ValidationEngine\Contracts;

use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationContext;
use App\Services\AcademicPlanning\ValidationEngine\DTO\ValidationResult;

interface RuleValidatorInterface
{
    public function validate(ValidationContext $context, ValidationResult $result): void;
}
