<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\AuditLog;

class AuditLogger
{
    public function log(string $action, string $entityType, int $entityId, array $before = [], array $after = [], ?int $actorId = null): void
    {
        AuditLog::create([
            'actor_id' => $actorId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'before_json' => $before,
            'after_json' => $after,
        ]);
    }
}
