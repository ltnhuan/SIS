<?php

namespace App\Modules\Timetabling\Models;

use App\Modules\Core\Models\BaseModel;

class TimetableConflict extends BaseModel
{
    protected $table = 'timetable_conflicts';
    public $timestamps = false;
}
