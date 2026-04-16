<?php

namespace App\Http\Resources\AcademicPlanning\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvisorDashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'advisor_id' => $this['advisor_id'] ?? null,
            'students_at_risk' => $this['students_at_risk'] ?? 0,
            'pending_reviews' => $this['pending_reviews'] ?? 0,
        ];
    }
}
