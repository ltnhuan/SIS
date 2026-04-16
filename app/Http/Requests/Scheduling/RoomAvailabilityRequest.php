<?php

namespace App\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

class RoomAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'thu' => ['required', 'integer', 'between:2,8'],
            'tiet' => ['required', 'integer', 'between:1,10'],
        ];
    }
}
