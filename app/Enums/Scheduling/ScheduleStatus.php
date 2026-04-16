<?php

namespace App\Enums\Scheduling;

enum ScheduleStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case CANCELLED = 'cancelled';
}
