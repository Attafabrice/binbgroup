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
use App\Notifications\QuoteRequestAccepted;
use App\Notifications\QuoteRequestRejected;
use DomainException;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\Notification;
use Throwable;
use Illuminate\Support\Facades\DB;

class QuoteRequest extends Model
{
    use SoftDeletes, LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'answers' => 'array',
        'status'  => QuoteRequestStatus::class,
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void{
        static::creating(function (QuoteRequest $r) {
            $r->reference ??= Sequence::next('quote_request');
            $r->token ??= Str::random(64);
            $r->status ??= QuoteRequestStatus::Nouvelle;
        });
    }

    public function getActivitylogOptions(): LogOptions{
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    public function service(): BelongsTo { 
        return $this->belongsTo(Service::class); 
    }

    public function client(): BelongsTo { 
        return $this->belongsTo(Client::class); 
    }
    public function assignee(): BelongsTo { 
        return $this->belongsTo(User::class, 'assigned_to'); 
    }

    public function quotes(): HasMany { 
        return $this->hasMany(Quote::class); 
    }
    public function documents(): MorphMany { 
        return $this->morphMany(Document::class, 'documentable'); 
    }

    public function reviewer(): BelongsTo{
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function startReview(): void{
        $this->ensureStatus([QuoteRequestStatus::Nouvelle]);
        $this->update([
            'status' => QuoteRequestStatus::EnCours,
            'assigned_to' => auth()->id(),
        ]);
    }

    /** @return bool true si l'email au client est parti */
    public function accept(?string $note = null): bool{
        $this->ensureStatus([QuoteRequestStatus::Nouvelle, QuoteRequestStatus::EnCours]);
        if (! $this->client_id) {
            throw new DomainException('Cette demande n’est rattachée à aucun client : créez d’abord sa fiche client.');
        }
        DB::transaction(function () use ($note) {
            $this->update([
                'status' => QuoteRequestStatus::Acceptee,
                'decision_note' => $note,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            if (! $this->quotes()->exists()) {
                Quote::create([
                    'quote_request_id' => $this->id,
                    'client_id' => $this->client_id,
                    'service_id' => $this->service_id,
                    'title' => 'Devis '.$this->service->name,
                    'valid_until' => now()->addDays(30),
                    'created_by' => auth()->id(),
                ]);
            }
        });
        return $this->notifyClient(new QuoteRequestAccepted($this));
    }

/** @return bool true si l'email au client est parti */
    public function reject(string $reason): bool{
        $this->ensureStatus([QuoteRequestStatus::Nouvelle, QuoteRequestStatus::EnCours]);
        $this->update([
            'status' => QuoteRequestStatus::Rejetee,
            'decision_note' => $reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return $this->notifyClient(new QuoteRequestRejected($this));
    }

    private function ensureStatus(array $allowed): void{
        if (! in_array($this->status, $allowed, true)) {
            throw new DomainException('Cette demande a déjà été traitée.');
        }
    }

    private function notifyClient(BaseNotification $notification): bool{
        if (blank($this->email)) {
            return false;
        }

        try {
            Notification::route('mail', [$this->email => $this->name])->notify($notification);

            return true;
        } catch (Throwable $e) {
            report($e); // la décision reste enregistrée même si l'email échoue

            return false;
        }
    }

    public function visibleDocuments(): MorphMany{
        return $this->morphMany(Document::class, 'documentable')->where('is_visible_to_client', true);
    }
}