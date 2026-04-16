<?php

namespace App\Http\Requests\AcademicPlanning;

use Illuminate\Foundation\Http\FormRequest;

class ValidateStudyPlanItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'candidate_schedule_slots' => ['sometimes', 'array'],
            'candidate_schedule_slots.*.day' => ['required_with:candidate_schedule_slots', 'integer', 'between:1,7'],
            'candidate_schedule_slots.*.start' => ['required_with:candidate_schedule_slots', 'date_format:H:i'],
            'candidate_schedule_slots.*.end' => ['required_with:candidate_schedule_slots', 'date_format:H:i'],
        ];
    }
}
