<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as Notifier;
use Throwable;

class TeamNotifier
{
    public static function send(Notification $n, array $roles): void
    {
        try {
            Notifier::send(User::role($roles)->where('is_active', true)->get(), $n);
        } catch (Throwable $e) {
            report($e);
        }
    }
}