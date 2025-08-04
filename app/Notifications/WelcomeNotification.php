<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    private $appName;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->appName = config('app.name');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Selamat datang di {$this->appName}!")
            ->greeting("Halo {$notifiable->name}!")
            ->line("Selamat datang di {$this->appName}! \n Akun Anda telah berhasil dibuat. \n")
            ->line('Silakan melakukan verifikasi email untuk mengaktifkan profil Anda.')
            ->line('Terima kasih telah bergabung dengan kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'registration_success',
            'title' => 'Selamat Datang!',
            'message' => "Selamat datang {$notifiable->name}! Akun Anda di {$this->appName} telah berhasil dibuat. Silakan melakukan verifikasi email untuk mengaktifkan profil Anda.",
            'icon' => 'user-check'
        ];
    }
}
