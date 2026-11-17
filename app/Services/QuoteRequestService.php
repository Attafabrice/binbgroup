<?php

namespace App\Services;

use App\Enums\QuoteRequestStatus;
use App\Models\{Quote, QuoteRequest, User};
use App\Notifications\{QuoteRequestAccepted, QuoteRequestRejected};
use DomainException;
use Illuminate\Notifications\Notification as BaseNotification;
use Illuminate\Support\Facades\{DB, Notification};
use Throwable;

class QuoteRequestService
{
    public function take(QuoteRequest $r, User $by): void
    {
        $this->ensureStatus($r, [QuoteRequestStatus::Nouvelle]);
        $r->update(['status' => QuoteRequestStatus::EnCours, 'assigned_to' => $by->id]);
    }

    /** @return bool true si l'email est parti */
    public function accept(QuoteRequest $r, User $by, ?string $note = null): bool
    {
        $this->ensureStatus($r, [QuoteRequestStatus::Nouvelle, QuoteRequestStatus::EnCours]);
        if (! $r->client_id) {
            throw new DomainException('Aucun client rattaché : créez d’abord sa fiche client.');
        }

        DB::transaction(function () use ($r, $by, $note) {
            $r->update([
                'status' => QuoteRequestStatus::Acceptee,
                'decision_note' => $note,
                'reviewed_by' => $by->id,
                'reviewed_at' => now(),
            ]);

            if (! $r->quotes()->exists()) {
                Quote::create([
                    'quote_request_id' => $r->id,
                    'client_id'        => $r->client_id,
                    'service_id'       => $r->service_id,
                    'title'            => 'Devis '.$r->service->name,
                    'valid_until'      => now()->addDays(30),
                    'created_by'       => $by->id,
                ]);
            }
        });

        return $this->notify($r, new QuoteRequestAccepted($r));
    }

    public function reject(QuoteRequest $r, User $by, string $reason): bool
    {
        $this->ensureStatus($r, [QuoteRequestStatus::Nouvelle, QuoteRequestStatus::EnCours]);
        $r->update([
            'status' => QuoteRequestStatus::Rejetee,
            'decision_note' => $reason,
            'reviewed_by' => $by->id,
            'reviewed_at' => now(),
        ]);

        return $this->notify($r, new QuoteRequestRejected($r));
    }

    private function ensureStatus(QuoteRequest $r, array $allowed): void
    {
        if (! in_array($r->status, $allowed, true)) {
            throw new DomainException('Cette demande a déjà été traitée.');
        }
    }

    private function notify(QuoteRequest $r, BaseNotification $n): bool
    {
        if (blank($r->email)) {
            return false;
        }
        try {
            Notification::route('mail', [$r->email => $r->name])->notify($n);
            return true;
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }
}