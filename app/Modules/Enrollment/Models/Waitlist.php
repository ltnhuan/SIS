<?php

namespace App\Modules\Enrollment\Models;

use App\Modules\Core\Models\BaseModel;

class Waitlist extends BaseModel
{
    protected $table = 'waitlists';
    public $timestamps = false;
}
