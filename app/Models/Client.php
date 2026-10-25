<?php

namespace App\Models;

use App\Enums\ClientType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Client extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = ['type' => ClientType::class];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?: $this->contact_name;
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function quoteRequests(): HasMany { return $this->hasMany(QuoteRequest::class); }
    public function quotes(): HasMany { return $this->hasMany(Quote::class); }
    public function projects(): HasMany { return $this->hasMany(Project::class); }
    public function interventions(): HasMany { return $this->hasMany(Intervention::class); }
    public function documents(): HasMany { return $this->hasMany(Document::class); }
}