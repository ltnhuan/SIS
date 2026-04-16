<?php

namespace App\Http\Resources\AcademicPlanning\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentDashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'forecast' => $this['forecast'] ?? [],
            'progress' => $this['progress'] ?? [],
            'workload' => $this['workload'] ?? [],
            'risk' => $this['risk'] ?? [],
        ];
    }
}
