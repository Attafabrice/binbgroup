<?php

namespace App\Models;

use App\Enums\QuoteRequestStatus;
use App\Enums\QuoteStatus;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
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
        'notes', 'valid_until', 'client_id', 'service_id', 'quote_request_id',
    ];

    protected static function booted(): void{
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

    public function getActivitylogOptions(): LogOptions{
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public static function money(int|float|string|null $amount): string{
        return number_format((int) $amount, 0, ',', ' ').' FCFA';
    }

    public function getTaxAmountAttribute(): int{
        return (int) $this->total - (int) $this->subtotal;
    }

    public function isLocked(): bool{
        return $this->sent_at !== null;
    }

    public function recalculate(): void{
        $subtotal = (int) $this->items()->get()->sum(fn (QuoteItem $i) => $i->total);
        $total    = $subtotal + (int) round($subtotal * $this->tax_rate / 100);

        $this->forceFill(['subtotal' => $subtotal, 'total' => $total])->saveQuietly();
    }

    public function send(): void{
        $this->refresh();

        if ($this->isLocked()) {
            throw new DomainException('Ce devis a déjà été envoyé.');
        }
        if ($this->items()->doesntExist() || $this->total <= 0) {
            throw new DomainException('Le devis doit contenir au moins une ligne et un montant supérieur à zéro.');
        }

        DB::transaction(function () {
            $this->update(['status' => QuoteStatus::Envoye, 'sent_at' => now()]);
            $this->quoteRequest?->update(['status' => QuoteRequestStatus::DevisEnvoye]);
        });
        // TODO : générer le PDF (dompdf), le stocker dans documents et prévenir le client
    }

    /** Accord du client : le devis devient un projet. */
    public function accept(): Project{
        if ($this->status !== QuoteStatus::Envoye) {
            throw new DomainException('Seul un devis envoyé peut être accepté.');
        }

        return DB::transaction(function () {
            $this->update(['status' => QuoteStatus::Accepte, 'accepted_at' => now()]);
            $project = Project::createFromQuote($this);
            $this->quoteRequest?->update(['status' => QuoteRequestStatus::Convertie]);

            return $project;
        });
    }

    public function refuse(): void{
        if ($this->status !== QuoteStatus::Envoye) {
            throw new DomainException('Seul un devis envoyé peut être refusé.');
        }

        $this->update(['status' => QuoteStatus::Refuse, 'refused_at' => now()]);
    }

    public function client(): BelongsTo { 
        return $this->belongsTo(Client::class); 
    }
    public function service(): BelongsTo { 
        return $this->belongsTo(Service::class); 
    }
    public function quoteRequest(): BelongsTo { 
        return $this->belongsTo(QuoteRequest::class); 
    }
    public function creator(): BelongsTo { 
        return $this->belongsTo(User::class, 'created_by'); 
    }
    public function items(): HasMany { 
        return $this->hasMany(QuoteItem::class)->orderBy('position'); 
    }
    public function project(): HasOne { 
        return $this->hasOne(Project::class); 
    }
    public function documents(): MorphMany { 
        return $this->morphMany(Document::class, 'documentable'); 
    }
}