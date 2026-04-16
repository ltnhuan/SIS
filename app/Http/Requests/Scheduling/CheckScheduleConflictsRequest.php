<?php

namespace App\Http\Requests\Scheduling;

use Illuminate\Foundation\Http\FormRequest;

class CheckScheduleConflictsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'gv_id' => ['required', 'integer', 'exists:users,id'],
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'lop_id' => ['required', 'integer', 'exists:classes,id'],
            'thu' => ['required', 'integer', 'between:2,8'],
            'tiet_bat_dau' => ['required', 'integer', 'between:1,10'],
            'so_tiet' => ['required', 'integer', 'between:1,6'],
            'tuan_bat_dau' => ['required', 'integer', 'between:1,30'],
            'tuan_ket_thuc' => ['required', 'integer', 'gte:tuan_bat_dau'],
            'ghi_chu' => ['nullable', 'string', 'max:200'],
            'trang_thai' => ['nullable', 'string'],
        ];
    }
}
