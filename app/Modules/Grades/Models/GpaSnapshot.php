<?php

namespace App\Modules\Grades\Models;

use App\Modules\Core\Models\BaseModel;

class GpaSnapshot extends BaseModel
{
    protected $table = 'gpa_snapshots';
    public $timestamps = false;
}
