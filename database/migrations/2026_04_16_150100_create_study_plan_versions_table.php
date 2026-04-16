<?php

use App\Enums\AcademicPlanning\StudyPlanVersionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_plan_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_plan_id')->constrained('study_plans')->cascadeOnDelete();
            $table->unsignedSmallInteger('version_no');
            $table->string('status', 40)->default(StudyPlanVersionStatus::DRAFT->value);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('total_planned_credits')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('revision_requested_at')->nullable();
            $table->timestamps();

            $table->unique(['study_plan_id', 'version_no']);
            $table->index(['study_plan_id', 'status']);
            $table->index(['study_plan_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_versions');
    }
};
