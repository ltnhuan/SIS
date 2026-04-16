<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'semester_id',
        'room_id',
        'ngay_thi',
        'gio_bat_dau',
        'so_phut',
        'gv_coi_thi_json',
        'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'ngay_thi' => 'date',
            'gio_bat_dau' => 'datetime:H:i:s',
            'gv_coi_thi_json' => 'array',
        ];
    }
}
