<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ProjectPhoto extends Model
{
    protected $guarded = [];

    protected $casts = [
        'taken_at'             => 'datetime',
        'is_visible_to_client' => 'boolean',
    ];

    public function scopeVisibleToClient(Builder $q): Builder
    {
        return $q->where('is_visible_to_client', true);
    }

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function step(): BelongsTo { return $this->belongsTo(ProjectStep::class, 'project_step_id'); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}