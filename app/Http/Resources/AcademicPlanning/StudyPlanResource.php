<?php

namespace App\Http\Resources\AcademicPlanning;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'advisor_id' => $this->advisor_id,
            'academic_program_id' => $this->academic_program_id,
            'curriculum_id' => $this->curriculum_id,
            'status' => $this->status,
            'current_version_no' => $this->current_version_no,
            'submitted_at' => $this->submitted_at,
            'approved_at' => $this->approved_at,
            'latest_reviewer_note' => $this->latest_reviewer_note,
            'versions' => StudyPlanVersionResource::collection($this->whenLoaded('versions')),
        ];
    }
}
