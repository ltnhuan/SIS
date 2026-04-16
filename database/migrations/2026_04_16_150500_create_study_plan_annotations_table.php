<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('study_plan_annotations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('study_plan_version_id')->constrained('study_plan_versions')->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained('advisors');
            $table->foreignId('study_plan_item_id')->nullable()->constrained('study_plan_items')->nullOnDelete();
            $table->text('annotation');
            $table->timestamps();

            $table->index(['study_plan_version_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_annotations');
    }
};
