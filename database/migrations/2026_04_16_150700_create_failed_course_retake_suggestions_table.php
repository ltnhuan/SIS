<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('failed_course_retake_suggestions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('study_plan_version_id')->nullable()->constrained('study_plan_versions')->nullOnDelete();
            $table->foreignId('suggested_semester_id')->nullable()->constrained('semesters')->nullOnDelete();
            $table->string('status', 30)->default('pending');
            $table->json('meta')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->unique(['student_id', 'course_id', 'study_plan_version_id'], 'retake_suggestions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_course_retake_suggestions');
    }
};
