<?php

namespace App\Modules\Core\Models;

class Policy extends BaseModel
{
    protected $table = 'policies';
    protected $casts = [
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'is_enabled' => 'boolean',
    ];
}
