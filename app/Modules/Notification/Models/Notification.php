<?php

namespace App\Modules\Notification\Models;

use App\Modules\Core\Models\BaseModel;

class Notification extends BaseModel
{
    protected $table = 'notifications';
    public $timestamps = false;
}
