<?php

namespace App\Http\Requests\AcademicPlanning;

use Illuminate\Foundation\Http\FormRequest;

class SubmitStudyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
