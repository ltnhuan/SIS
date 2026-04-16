<?php

use App\Enums\AcademicPlanning\RiskLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_plan_risk_flags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_plan_version_id')->constrained('study_plan_versions')->cascadeOnDelete();
            $table->string('risk_level', 20)->default(RiskLevel::LOW->value);
            $table->string('flag_type', 60);
            $table->text('message');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['study_plan_version_id', 'risk_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_risk_flags');
    }
};
