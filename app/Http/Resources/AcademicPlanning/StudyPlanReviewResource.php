<?php

namespace App\Http\Resources\AcademicPlanning;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'study_plan_version_id' => $this->study_plan_version_id,
            'advisor_id' => $this->advisor_id,
            'status' => $this->status,
            'comment' => $this->comment,
            'reviewed_at' => $this->reviewed_at,
        ];
    }
}
