<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Service extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(fn (Service $s) => $s->slug ??= Str::slug($s->name));
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)->orderBy('position');
    }

    public function fields(): HasMany { return $this->hasMany(ServiceField::class)->orderBy('position'); }
    public function stepTemplates(): HasMany { return $this->hasMany(StepTemplate::class)->orderBy('position'); }
    public function quoteRequests(): HasMany { return $this->hasMany(QuoteRequest::class); }
    public function projects(): HasMany { return $this->hasMany(Project::class); }
}