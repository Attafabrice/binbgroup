<?php

namespace App\Services;

use App\Enums\{QuoteRequestStatus, QuoteStatus};
use App\Models\{Project, Quote, User};
use App\Notifications\QuoteSent;
use DomainException;
use Illuminate\Support\Facades\{DB, Notification};
use App\Notifications\TeamAlert;
use App\Support\TeamNotifier;
use Throwable;

class QuoteService
{
    /** @return bool true si l'email est parti */
    public function send(Quote $q): bool{
        $q->refresh();

        if ($q->isLocked()) {
            throw new DomainException('Ce devis a déjà été envoyé.');
        }
        if ($q->items()->doesntExist() || $q->total <= 0) {
            throw new DomainException('Le devis doit contenir au moins une ligne et un montant supérieur à zéro.');
        }

        DB::transaction(function () use ($q) {
            $q->update(['status' => QuoteStatus::Envoye, 'sent_at' => now()]);
            $q->quoteRequest?->update(['status' => QuoteRequestStatus::DevisEnvoye]);
        });

        $email = $q->client?->email ?? $q->client?->user?->email ?? $q->quoteRequest?->email;
        if (blank($email)) {
            return false;
        }

        try {
            Notification::route('mail', $email)->notify(new QuoteSent($q));
            return true;
        } catch (Throwable $e) {
            report($e); // l'envoi du devis reste valide
            return false;
        }
    }

    /** Client (portail) ou admin (hors ligne) : même chemin. */
    public function accept(Quote $quote, ?User $by = null, string $channel = 'portal', ?string $note = null, ?string $proof = null): Project{
        $project = DB::transaction(function () use ($quote, $by, $channel, $note, $proof) {
            $q = Quote::lockForUpdate()->findOrFail($quote->id);

            if ($q->status !== QuoteStatus::Envoye) {
                throw new DomainException('Seul un devis envoyé peut être accepté.');
            }
            if ($q->isExpired()) {
                throw new DomainException('Ce devis a expiré.');
            }

            $q->update([
                'status' => QuoteStatus::Accepte, 'accepted_at' => now(), 'accepted_by' => $by?->id,
                'acceptance_channel' => $channel, 'acceptance_note' => $note, 'acceptance_proof' => $proof,
            ]);
            $q->quoteRequest?->update(['status' => QuoteRequestStatus::Convertie]);
            return Project::createFromQuote($q);
        });

        TeamNotifier::send(new TeamAlert(
            'Devis accepté '.$quote->reference,
            ($quote->client?->display_name).' · projet '.$project->reference.' créé'.($by ? ' (enregistré par '.$by->name.')' : ''),
            route('admin.projects.show', $project)
        ), ['super_admin', 'admin', 'commercial', 'chef_projet']);

        return $project;
    }

    public function refuse(Quote $quote, ?string $reason = null): void{
        DB::transaction(function () use ($quote, $reason) {
            $q = Quote::lockForUpdate()->findOrFail($quote->id);

            if ($q->status !== QuoteStatus::Envoye) {
                throw new DomainException('Seul un devis envoyé peut être refusé.');
            }
            $q->update(['status' => QuoteStatus::Refuse, 'refused_at' => now(), 'refusal_reason' => $reason]);
        });

        TeamNotifier::send(new TeamAlert(
            'Devis refusé '.$quote->reference,
            ($quote->client?->display_name).($reason ? ' : '.$reason : ''),
            route('admin.quotes.show', $quote)
        ), ['super_admin', 'admin', 'commercial']);
    }
}