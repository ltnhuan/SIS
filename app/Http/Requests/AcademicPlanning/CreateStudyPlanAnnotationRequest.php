<?php

namespace App\Http\Requests\AcademicPlanning;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudyPlanAnnotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'study_plan_item_id' => ['nullable', 'integer', 'exists:study_plan_items,id'],
            'annotation' => ['required', 'string', 'max:1500'],
        ];
    }
}
