<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $guarded = [];
    protected $casts = [
        'params_json' => 'array',
        'equipment_json' => 'array',
        'status_json' => 'array',
        'workload_json' => 'array',
        'skills_json' => 'array',
        'before_json' => 'array',
        'after_json' => 'array',
        'payload_json' => 'array',
        'history_json' => 'array',
        'solver_input_json' => 'array',
        'solver_output_json' => 'array',
        'detail_json' => 'array',
        'members_json' => 'array',
        'content_json' => 'array',
    ];
}
