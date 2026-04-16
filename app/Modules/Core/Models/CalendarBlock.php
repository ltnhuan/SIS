<?php

namespace App\Modules\Core\Models;

class CalendarBlock extends BaseModel
{
    protected $table = 'calendar_blocks';
    protected $casts = [
        'start_dt' => 'datetime',
        'end_dt' => 'datetime',
    ];
}
