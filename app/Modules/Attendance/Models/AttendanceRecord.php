<?php

namespace App\Modules\Attendance\Models;

use App\Modules\Core\Models\BaseModel;

class AttendanceRecord extends BaseModel
{
    protected $table = 'attendance_records';
    public $timestamps = false;
}
