<?php

use App\Enums\AcademicPlanning\StudyPlanReviewStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_plan_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_plan_version_id')->constrained('study_plan_versions')->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained('advisors');
            $table->string('status', 40)->default(StudyPlanReviewStatus::PENDING->value);
            $table->text('comment')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['advisor_id', 'status']);
            $table->index(['study_plan_version_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_reviews');
    }
};
