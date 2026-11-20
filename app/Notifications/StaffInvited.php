<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffInvited extends Notification
{
    public function __construct(public string $token) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage{
        return (new MailMessage)
            ->subject("Votre accès au back-office BinB' group")
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Un compte vient d’être créé pour vous. Choisissez votre mot de passe pour activer l’accès.')
            ->action('Choisir mon mot de passe', route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]))
            ->line('Ce lien expire dans 60 minutes. Passé ce délai, demandez un nouveau lien à votre administrateur.')
            ->salutation("L'équipe BinB' group");
    }
}