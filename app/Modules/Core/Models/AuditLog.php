<?php

namespace App\Modules\Core\Models;

class AuditLog extends BaseModel
{
    protected $table = 'audit_logs';
    public $timestamps = false;
}
