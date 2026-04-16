<?php

namespace App\Modules\Enrollment\Models;

use App\Modules\Core\Models\BaseModel;

class Enrollment extends BaseModel
{
    protected $table = 'enrollments';
    public $timestamps = false;
}
