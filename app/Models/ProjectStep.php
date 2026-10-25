<?php

namespace App\Models;

use App\Enums\StepStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ProjectStep extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status'               => StepStatus::class,
        'planned_date'         => 'date',
        'completed_at'         => 'datetime',
        'is_visible_to_client' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (ProjectStep $s) {
            $s->status ??= StepStatus::AFaire;
            $s->completed_at = $s->status === StepStatus::Termine ? ($s->completed_at ?? now()) : null;
        });

        static::saved(fn (ProjectStep $s) => $s->project->recalculateProgress());
        static::deleted(fn (ProjectStep $s) => $s->project->recalculateProgress());
    }

    public function scopeVisibleToClient(Builder $q): Builder
    {
        return $q->where('is_visible_to_client', true);
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
}