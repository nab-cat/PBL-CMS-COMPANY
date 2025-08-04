<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventRegistrationNotification extends Notification
{
    use Queueable;

    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pengingat Event: ' . $this->event->nama_event)
            ->greeting('Halo ' . $notifiable->name)
            ->line("Anda berhasil mendaftar ke event: {$this->event->nama_event}.")
            ->action('Lihat Event', url('/event/' . $this->event->slug))
            ->line('Terima kasih telah mendaftar.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'event_registration',
            'title' => 'Pendaftaran Event Berhasil',
            'message' => "Anda telah terdaftar di event: {$this->event->nama_event}.",
            'icon' => 'bell',
            'action_url' => url('/event/' . $this->event->slug),
            'action_text' => 'Lihat Event',
        ];
    }
}
