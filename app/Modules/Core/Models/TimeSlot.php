<?php

namespace App\Modules\Core\Models;

class TimeSlot extends BaseModel
{
    protected $table = 'time_slots';
    protected $casts = [
        'starts_at' => 'datetime:H:i',
        'ends_at' => 'datetime:H:i',
        'is_enabled' => 'boolean',
    ];
}
