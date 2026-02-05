<?php

namespace App\Modules\Core\Services;

use Illuminate\Support\Facades\DB;

class RbacService
{
    public function userHasRoleInScope(int $userId, array $roleCodes, string $scopeType, int $scopeId): bool
    {
        return DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $userId)
            ->whereIn('roles.code', $roleCodes)
            ->where('role_user.scope_type', $scopeType)
            ->where('role_user.scope_id', $scopeId)
            ->exists();
    }
}
