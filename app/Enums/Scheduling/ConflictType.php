<?php

namespace App\Enums\Scheduling;

enum ConflictType: string
{
    case TEACHER_CONFLICT = 'GV_CONFLICT';
    case ROOM_CONFLICT = 'ROOM_CONFLICT';
    case CLASS_CONFLICT = 'CLASS_CONFLICT';
    case TEACHER_OVERLOAD = 'TEACHER_OVERLOAD';
}
