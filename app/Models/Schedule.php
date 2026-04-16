<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'gv_id',
        'room_id',
        'semester_id',
        'lop_id',
        'thu',
        'tiet_bat_dau',
        'so_tiet',
        'tuan_bat_dau',
        'tuan_ket_thuc',
        'trang_thai',
        'ghi_chu',
    ];
}
