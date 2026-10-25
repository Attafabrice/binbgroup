<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class QuoteItem extends Model
{
    protected $guarded = [];

    protected $casts = ['quantity' => 'decimal:2'];

    protected static function booted(): void
    {
        $guard = function (QuoteItem $item) {
            if ($item->quote?->isLocked()) {
                throw new DomainException('Un devis envoyé ne peut plus être modifié.');
            }
        };

        static::creating($guard);
        static::updating($guard);
        static::deleting($guard);

        static::saved(fn (QuoteItem $i) => $i->quote->recalculate());
        static::deleted(fn (QuoteItem $i) => $i->quote->recalculate());
    }

    public function getTotalAttribute(): int
    {
        return (int) round((float) $this->quantity * $this->unit_price);
    }

    public function quote(): BelongsTo { return $this->belongsTo(Quote::class); }
}