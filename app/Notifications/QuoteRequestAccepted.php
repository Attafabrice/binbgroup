<?php

namespace App\Notifications;

use App\Models\QuoteRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteRequestAccepted extends Notification
{
    public function __construct(public QuoteRequest $quoteRequest) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $r = $this->quoteRequest;

        $mail = (new MailMessage)
            ->subject("Votre demande {$r->reference} a été validée")
            ->greeting("Bonjour {$r->name},")
            ->line("Après étude, votre demande de devis **{$r->reference}** pour le service **{$r->service->name}** a été validée.")
            ->line('Notre équipe prépare votre devis chiffré. Vous le recevrez très prochainement et pourrez le consulter dans votre espace client.');

        if (filled($r->decision_note)) {
            $mail->line('Message de notre équipe : '.$r->decision_note);
        }

        return $mail
            ->action('Suivre ma demande', url('/espace-client/demandes/'.$r->id))
            ->salutation("L'équipe BinB' group");
    }
}