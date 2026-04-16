<?php

namespace App\Http\Requests\AcademicPlanning;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudyPlanVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_primary' => ['sometimes', 'boolean'],
            'clone_from_version_id' => ['nullable', 'integer', 'exists:study_plan_versions,id'],
        ];
    }
}
