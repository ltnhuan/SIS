<?php

namespace App\Http\Resources\AcademicPlanning;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'study_plan_semester_id' => $this->study_plan_semester_id,
            'status' => $this->status,
            'credits_snapshot' => $this->credits_snapshot,
            'is_prerequisite_satisfied' => $this->is_prerequisite_satisfied,
            'is_credit_overload' => $this->is_credit_overload,
            'is_failed_retake' => $this->is_failed_retake,
            'validation_payload' => $this->validation_payload,
        ];
    }
}
