<?php

namespace App\Modules\Attendance\Models;

use App\Modules\Core\Models\BaseModel;

class AttendanceSession extends BaseModel
{
    protected $table = 'attendance_sessions';
    public $timestamps = false;
}
