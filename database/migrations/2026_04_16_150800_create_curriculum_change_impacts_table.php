<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('curriculum_change_impacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curricula');
            $table->foreignId('study_plan_id')->nullable()->constrained('study_plans')->nullOnDelete();
            $table->string('change_type', 50);
            $table->json('payload');
            $table->timestamp('detected_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['curriculum_id', 'detected_at']);
            $table->index(['processed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_change_impacts');
    }
};
