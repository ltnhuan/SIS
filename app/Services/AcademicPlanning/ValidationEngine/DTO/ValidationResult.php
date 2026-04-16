<?php

namespace App\Services\AcademicPlanning\ValidationEngine\DTO;

use App\Enums\AcademicPlanning\Validation\ValidationOutcome;
use App\Enums\AcademicPlanning\Validation\ValidationSeverity;

class ValidationResult
{
    /** @var array<int, ValidationIssue> */
    private array $issues = [];

    public function addIssue(ValidationIssue $issue): void
    {
        $this->issues[] = $issue;
    }

    public function issues(): array
    {
        return $this->issues;
    }

    public function canSave(): bool
    {
        foreach ($this->issues as $issue) {
            if ($issue->severity === ValidationSeverity::BLOCKING->value) {
                return false;
            }
        }

        return true;
    }

    public function outcome(): string
    {
        if (! $this->canSave()) {
            return ValidationOutcome::BLOCKED->value;
        }

        foreach ($this->issues as $issue) {
            if ($issue->severity === ValidationSeverity::WARNING->value) {
                return ValidationOutcome::WARNING->value;
            }
        }

        return ValidationOutcome::VALID->value;
    }

    public function toArray(): array
    {
        return [
            'can_save' => $this->canSave(),
            'outcome' => $this->outcome(),
            'summary' => [
                'total_issues' => count($this->issues),
                'blocking_count' => count(array_filter($this->issues, fn (ValidationIssue $issue): bool => $issue->severity === ValidationSeverity::BLOCKING->value)),
                'warning_count' => count(array_filter($this->issues, fn (ValidationIssue $issue): bool => $issue->severity === ValidationSeverity::WARNING->value)),
                'info_count' => count(array_filter($this->issues, fn (ValidationIssue $issue): bool => $issue->severity === ValidationSeverity::INFO->value)),
            ],
            'issues' => array_map(static fn (ValidationIssue $issue): array => $issue->toArray(), $this->issues),
        ];
    }
}
