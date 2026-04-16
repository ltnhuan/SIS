<?php

namespace App\Modules\Timetabling\Models;

use App\Modules\Core\Models\BaseModel;

class TimetableAssignment extends BaseModel
{
    protected $table = 'timetable_assignments';
    public $timestamps = false;
}
