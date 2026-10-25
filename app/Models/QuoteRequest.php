<?php

namespace App\Models;

use App\Enums\QuoteRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class QuoteRequest extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'answers' => 'array',
        'status'  => QuoteRequestStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (QuoteRequest $r) {
            $r->reference ??= Sequence::next('quote_request');
            $r->token ??= Str::random(64);
            $r->status ??= QuoteRequestStatus::Nouvelle;
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function quotes(): HasMany { return $this->hasMany(Quote::class); }
    public function documents(): MorphMany { return $this->morphMany(Document::class, 'documentable'); }
}