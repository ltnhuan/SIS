<?php

namespace App\Http\Resources\AcademicPlanning\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrarDashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'approval_rate' => $this['approval_rate'] ?? 0,
            'high_risk_students' => $this['high_risk_students'] ?? 0,
            'forecast_demand_next_semester' => $this['forecast_demand_next_semester'] ?? [],
        ];
    }
}
