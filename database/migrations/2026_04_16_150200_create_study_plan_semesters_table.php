<?php

use App\Enums\AcademicPlanning\RiskLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_plan_semesters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_plan_version_id')->constrained('study_plan_versions')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters');
            $table->unsignedTinyInteger('order_in_plan');
            $table->unsignedSmallInteger('planned_credits')->default(0);
            $table->unsignedSmallInteger('max_credits')->default((int) config('academic_planning.max_credits_per_semester', 24));
            $table->string('risk_level', 20)->default(RiskLevel::LOW->value);
            $table->timestamps();

            $table->unique(['study_plan_version_id', 'semester_id']);
            $table->index(['study_plan_version_id', 'order_in_plan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_semesters');
    }
};
