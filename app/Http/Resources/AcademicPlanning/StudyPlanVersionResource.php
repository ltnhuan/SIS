<?php

namespace App\Http\Resources\AcademicPlanning;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'study_plan_id' => $this->study_plan_id,
            'version_no' => $this->version_no,
            'status' => $this->status,
            'is_primary' => $this->is_primary,
            'total_planned_credits' => $this->total_planned_credits,
            'submitted_at' => $this->submitted_at,
            'approved_at' => $this->approved_at,
            'revision_requested_at' => $this->revision_requested_at,
            'semesters' => StudyPlanSemesterResource::collection($this->whenLoaded('semesters')),
        ];
    }
}
