<?php

namespace App\Services\AcademicPlanning\ValidationEngine\DTO;

use App\Models\StudyPlanVersion;

class ValidationContext
{
    public function __construct(
        public readonly StudyPlanVersion $version,
        public readonly int $studentId,
        public readonly int $semesterId,
        public readonly int $courseId,
        public readonly int $courseCredits,
        public readonly array $candidateScheduleSlots = [],
    ) {
    }
}
