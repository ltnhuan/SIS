<?php

namespace App\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

class MoveScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'thu' => ['required', 'integer', 'between:2,8'],
            'tiet_bat_dau' => ['required', 'integer', 'between:1,10'],
            'so_tiet' => ['nullable', 'integer', 'between:1,6'],
            'tuan_bat_dau' => ['nullable', 'integer', 'between:1,30'],
            'tuan_ket_thuc' => ['nullable', 'integer', 'between:1,30'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'ghi_chu' => ['nullable', 'string', 'max:200'],
        ];
    }
}
