<?php

namespace App\Http\Requests\AcademicPlanning;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_program_id' => ['required', 'integer', 'exists:academic_programs,id'],
            'curriculum_id' => ['required', 'integer', 'exists:curricula,id'],
            'advisor_id' => ['nullable', 'integer', 'exists:advisors,id'],
        ];
    }
}
