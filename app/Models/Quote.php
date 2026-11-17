<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Quote extends Model
{
    use LogsActivity, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'status'      => QuoteStatus::class,
        'valid_until' => 'date',
        'sent_at'     => 'datetime',
        'accepted_at' => 'datetime',
        'refused_at'  => 'datetime',
    ];

    /**
     * Champs qui ne peuvent plus être modifiés une fois le devis envoyé.
     */
    private const LOCKED_FIELDS = [
        'title',
        'subtotal',
        'tax_rate',
        'total',
        'currency',
        'notes',
        'valid_until',
        'client_id',
        'service_id',
        'quote_request_id',
    ];

    /* -------------------------------------------------------------------------- */
    /*                            Événements du modèle                            */
    /* -------------------------------------------------------------------------- */

    protected static function booted(): void
    {
        static::creating(function (Quote $q) {
            $q->reference ??= Sequence::next('quote');
            $q->status    ??= QuoteStatus::Brouillon;
        });

        static::updating(function (Quote $q) {
            if ($q->getOriginal('sent_at') && $q->isDirty(self::LOCKED_FIELDS)) {
                throw new DomainException('Un devis envoyé ne peut plus être modifié.');
            }
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                             Activité & Casts                               */
    /* -------------------------------------------------------------------------- */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    /* -------------------------------------------------------------------------- */
    /*                                 Méthodes                                   */
    /* -------------------------------------------------------------------------- */

    public static function money(int|float|string|null $amount): string
    {
        return number_format((int) $amount, 0, ',', ' ').' FCFA';
    }

    public function getTaxAmountAttribute(): int
    {
        return (int) $this->total - (int) $this->subtotal;
    }

    public function isLocked(): bool
    {
        return $this->sent_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->valid_until !== null
            && $this->valid_until->copy()->endOfDay()->isPast();
    }

    public function recalculate(): void
    {
        $subtotal = (int) $this->items()->get()->sum(fn (QuoteItem $i) => $i->total);
        $total    = $subtotal + (int) round($subtotal * $this->tax_rate / 100);

        $this->forceFill([
            'subtotal' => $subtotal,
            'total'    => $total,
        ])->saveQuietly();
    }

    /* -------------------------------------------------------------------------- */
    /*                                 Relations                                  */
    /* -------------------------------------------------------------------------- */

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('position');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}