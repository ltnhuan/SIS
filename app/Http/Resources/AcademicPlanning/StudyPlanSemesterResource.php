<?php

namespace App\Http\Resources\AcademicPlanning;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanSemesterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'semester_id' => $this->semester_id,
            'order_in_plan' => $this->order_in_plan,
            'planned_credits' => $this->planned_credits,
            'max_credits' => $this->max_credits,
            'risk_level' => $this->risk_level,
            'items' => StudyPlanItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
