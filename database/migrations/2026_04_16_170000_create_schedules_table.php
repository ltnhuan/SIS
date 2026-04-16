<?php

use App\Enums\Scheduling\ScheduleStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('gv_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('lop_id')->constrained('classes')->cascadeOnDelete();
            $table->unsignedTinyInteger('thu');
            $table->unsignedTinyInteger('tiet_bat_dau');
            $table->unsignedTinyInteger('so_tiet');
            $table->unsignedTinyInteger('tuan_bat_dau');
            $table->unsignedTinyInteger('tuan_ket_thuc');
            $table->string('trang_thai', 20)->default(ScheduleStatus::DRAFT->value);
            $table->string('ghi_chu', 200)->nullable();
            $table->timestamps();

            $table->index(['semester_id', 'thu', 'tiet_bat_dau']);
            $table->index(['gv_id', 'semester_id']);
            $table->index(['room_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
