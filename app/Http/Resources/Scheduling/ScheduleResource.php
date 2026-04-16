<?php

namespace App\Http\Resources\Scheduling;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'gv_id' => $this->gv_id,
            'room_id' => $this->room_id,
            'semester_id' => $this->semester_id,
            'lop_id' => $this->lop_id,
            'thu' => $this->thu,
            'tiet_bat_dau' => $this->tiet_bat_dau,
            'so_tiet' => $this->so_tiet,
            'tuan_bat_dau' => $this->tuan_bat_dau,
            'tuan_ket_thuc' => $this->tuan_ket_thuc,
            'trang_thai' => $this->trang_thai,
            'ghi_chu' => $this->ghi_chu,
        ];
    }
}
