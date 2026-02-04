<?php

namespace App\Modules\Attendance\Models;

use App\Modules\Core\Models\BaseModel;

class ExamEligibility extends BaseModel
{
    protected $table = 'exam_eligibilities';
    public $timestamps = false;
}
