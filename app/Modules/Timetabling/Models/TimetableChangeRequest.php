<?php

namespace App\Modules\Timetabling\Models;

use App\Modules\Core\Models\BaseModel;

class TimetableChangeRequest extends BaseModel
{
    protected $table = 'timetable_change_requests';
    public $timestamps = false;
}
