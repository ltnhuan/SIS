<?php

namespace App\Services\AcademicPlanning\ValidationEngine\DTO;

class ValidationIssue
{
    public function __construct(
        public readonly string $code,
        public readonly string $severity,
        public readonly string $message,
        public readonly array $meta = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'severity' => $this->severity,
            'message' => $this->message,
            'meta' => $this->meta,
        ];
    }
}
