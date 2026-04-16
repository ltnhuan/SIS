<?php

namespace App\Services\AcademicPlanning;

use App\Enums\AcademicPlanning\RiskLevel;
use App\Models\StudyPlanSemester;

class WorkloadAnalysisService
{
    public function checkCreditOverload(StudyPlanSemester $semester, int $additionalCredits): array
    {
        $newTotal = $semester->planned_credits + $additionalCredits;
        $isOverload = $newTotal > $semester->max_credits;

        return [
            'is_overload' => $isOverload,
            'planned_credits' => $newTotal,
            'max_credits' => $semester->max_credits,
            'risk_level' => $isOverload ? RiskLevel::HIGH->value : RiskLevel::LOW->value,
        ];
    }
}
