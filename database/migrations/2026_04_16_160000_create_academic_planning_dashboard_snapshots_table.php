<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('academic_planning_dashboard_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('study_plan_version_id')->nullable()->constrained('study_plan_versions')->nullOnDelete();
            $table->date('snapshot_date');
            $table->json('forecast_payload');
            $table->json('progress_payload');
            $table->json('workload_payload');
            $table->json('risk_payload');
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['student_id', 'snapshot_date'], 'academic_planning_snapshot_student_date_unique');
            $table->index(['study_plan_version_id', 'snapshot_date'], 'academic_planning_snapshot_version_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_planning_dashboard_snapshots');
    }
};
