<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumChangeImpact extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_id',
        'study_plan_id',
        'change_type',
        'payload',
        'detected_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'detected_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function studyPlan(): BelongsTo
    {
        return $this->belongsTo(StudyPlan::class);
    }
}
