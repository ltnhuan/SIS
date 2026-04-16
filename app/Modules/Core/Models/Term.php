<?php

namespace App\Modules\Core\Models;

class Term extends BaseModel
{
    protected $table = 'terms';
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
