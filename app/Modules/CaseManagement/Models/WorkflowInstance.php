<?php

namespace App\Modules\CaseManagement\Models;

use App\Modules\Core\Models\BaseModel;

class WorkflowInstance extends BaseModel
{
    protected $table = 'workflow_instances';
    public $timestamps = false;
}
