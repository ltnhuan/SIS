<?php

namespace App\Events\Scheduling;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimetablePublished
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public readonly int $semesterId, public readonly int $publishedBy)
    {
    }
}
