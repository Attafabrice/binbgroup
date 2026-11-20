<?php

namespace App\Notifications;

use App\Models\QuoteRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteRequestRejected extends Notification
{
    public function __construct(public QuoteRequest $quoteRequest) {}

    public function via(object $notifiable): array{
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage{
        $r = $this->quoteRequest;

        return (new MailMessage)
            ->subject("Votre demande {$r->reference} n'a pas pu être retenue")
            ->greeting("Bonjour {$r->name},")
            ->line("Nous avons étudié votre demande de devis **{$r->reference}** pour le service **{$r->service->name}**. Nous sommes au regret de ne pas pouvoir y donner suite.")
            ->line('Motif : '.$r->decision_note)
            ->line("Vous pouvez nous contacter pour en discuter ou déposer une nouvelle demande depuis votre espace client.")
            ->action('Accéder à mon espace', route('client.requests.show', $r))
            ->salutation("L'équipe BinB' group");
    }
}