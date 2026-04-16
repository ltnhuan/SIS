<?php

namespace App\Http\Requests\AcademicPlanning;

use App\Enums\AcademicPlanning\StudyPlanReviewStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ReviewStudyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(StudyPlanReviewStatus::class)],
            'comment' => ['nullable', 'string', 'max:1500'],
        ];
    }
}
