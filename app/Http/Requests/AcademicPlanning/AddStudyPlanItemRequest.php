<?php

namespace App\Http\Requests\AcademicPlanning;

use Illuminate\Foundation\Http\FormRequest;

class AddStudyPlanItemRequest extends FormRequest
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
        ];
    }
}
