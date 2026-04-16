<?php

use App\Enums\AcademicPlanning\StudyPlanItemStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_plan_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_plan_version_id')->constrained('study_plan_versions')->cascadeOnDelete();
            $table->foreignId('study_plan_semester_id')->constrained('study_plan_semesters')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses');
            $table->string('status', 30)->default(StudyPlanItemStatus::PLANNED->value);
            $table->unsignedTinyInteger('credits_snapshot');
            $table->boolean('is_prerequisite_satisfied')->default(false);
            $table->boolean('is_credit_overload')->default(false);
            $table->boolean('is_failed_retake')->default(false);
            $table->json('validation_payload')->nullable();
            $table->timestamps();

            $table->unique(['study_plan_version_id', 'course_id']);
            $table->index(['study_plan_semester_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_items');
    }
};
