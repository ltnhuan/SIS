<?php

namespace App\Modules\CaseManagement\Models;

use App\Modules\Core\Models\BaseModel;

class Ticket extends BaseModel
{
    protected $table = 'tickets';
    public $timestamps = false;
}
