<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyPlanReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_plan_version_id',
        'advisor_id',
        'status',
        'comment',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(StudyPlanVersion::class, 'study_plan_version_id');
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(Advisor::class);
    }
}
