<?php

namespace App\Modules\Grades\Models;

use App\Modules\Core\Models\BaseModel;

class GradeEntry extends BaseModel
{
    protected $table = 'grade_entries';
    public $timestamps = false;
}
