<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DemoChay;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        DemoChay::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Demo không cần schedule.
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
