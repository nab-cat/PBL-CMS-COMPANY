<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmailVerifiedNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'email_verified',
            'title' => 'Email Diverifikasi!',
            'message' => "Terima kasih {$notifiable->name}! Email Anda telah berhasil diverifikasi.",
            'icon' => 'mail-check'
        ];
    }
}
