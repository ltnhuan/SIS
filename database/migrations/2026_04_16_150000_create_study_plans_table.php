<?php

use App\Enums\AcademicPlanning\StudyPlanStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('advisor_id')->nullable()->constrained('advisors');
            $table->foreignId('academic_program_id')->constrained('academic_programs');
            $table->foreignId('curriculum_id')->constrained('curricula');
            $table->string('status', 40)->default(StudyPlanStatus::DRAFT->value);
            $table->unsignedSmallInteger('current_version_no')->default(1);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('latest_reviewer_note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'academic_program_id'], 'study_plans_student_program_unique');
            $table->index(['advisor_id', 'status'], 'study_plans_advisor_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plans');
    }
};
