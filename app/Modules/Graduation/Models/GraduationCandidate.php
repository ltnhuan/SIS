<?php

namespace App\Modules\Graduation\Models;

use App\Modules\Core\Models\BaseModel;

class GraduationCandidate extends BaseModel
{
    protected $table = 'graduation_candidates';
    public $timestamps = false;
}
