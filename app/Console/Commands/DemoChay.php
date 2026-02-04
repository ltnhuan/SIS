<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Notification\Models\Notification;

class DemoChay extends Command
{
    protected $signature = 'demo:chay';
    protected $description = 'Chạy demo SIS và tạo dữ liệu tổng hợp';

    public function handle(): int
    {
        Notification::create([
            'tenant_id' => 1,
            'source_type' => 'system',
            'source_id' => 0,
            'category' => 'System',
            'severity' => 'Trung',
            'recipient_type' => 'admin',
            'recipient_id' => 1,
            'title' => 'Demo SIS đã sẵn sàng',
            'body' => 'Dữ liệu mẫu đã được nạp. Truy cập /quan-tri để xem dashboard.',
            'payload_json' => [],
            'created_at' => now(),
        ]);

        $this->info('Demo SIS đã sẵn sàng.');

        return Command::SUCCESS;
    }
}
