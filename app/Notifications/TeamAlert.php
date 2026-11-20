<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamAlert extends Notification
{
    public function __construct(public string $title, public string $body, public string $url) {}

    public function via(object $notifiable): array { return ['database', 'mail']; }

    public function toArray(object $notifiable): array
    {
        return ['title' => $this->title, 'body' => $this->body, 'url' => $this->url];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->body)
            ->action('Ouvrir dans le back-office', $this->url);
    }
}