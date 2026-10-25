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
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Quote extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'status'      => QuoteStatus::class,
        'valid_until' => 'date',
        'sent_at'     => 'datetime',
        'accepted_at' => 'datetime',
        'refused_at'  => 'datetime',
    ];

    private const LOCKED_FIELDS = [
        'title', 'subtotal', 'tax_rate', 'total', 'currency',
        'notes', 'valid_until', 'client_id', 'quote_request_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quote $q) {
            $q->reference ??= Sequence::next('quote');
            $q->status ??= QuoteStatus::Brouillon;
        });

        static::updating(function (Quote $q) {
            if ($q->getOriginal('sent_at') && $q->isDirty(self::LOCKED_FIELDS)) {
                throw new DomainException('Un devis envoyé ne peut plus être modifié.');
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function isLocked(): bool
    {
        return $this->sent_at !== null;
    }

    public function recalculate(): void
    {
        $subtotal = (int) $this->items()->get()->sum(fn (QuoteItem $i) => $i->total);
        $total    = $subtotal + (int) round($subtotal * $this->tax_rate / 100);

        $this->forceFill(['subtotal' => $subtotal, 'total' => $total])->saveQuietly();
    }

    public function send(): void
    {
        if ($this->isLocked()) {
            throw new DomainException('Ce devis a déjà été envoyé.');
        }
        if ($this->items()->doesntExist()) {
            throw new DomainException('Un devis doit contenir au moins une ligne.');
        }

        $this->update(['status' => QuoteStatus::Envoye, 'sent_at' => now()]);
        // TODO : générer le PDF (dompdf) et créer le Document (type devis, visible client)
    }

    public function accept(): void
    {
        $this->update(['status' => QuoteStatus::Accepte, 'accepted_at' => now()]);
    }

    public function refuse(): void
    {
        $this->update(['status' => QuoteStatus::Refuse, 'refused_at' => now()]);
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function quoteRequest(): BelongsTo { return $this->belongsTo(QuoteRequest::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany { return $this->hasMany(QuoteItem::class)->orderBy('position'); }
    public function project(): HasOne { return $this->hasOne(Project::class); }
    public function documents(): MorphMany { return $this->morphMany(Document::class, 'documentable'); }
}