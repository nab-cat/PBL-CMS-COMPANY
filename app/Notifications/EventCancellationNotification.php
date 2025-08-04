<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCancellationNotification extends Notification
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
            ->subject('Pembatalan Event: ' . $this->event->nama_event)
            ->greeting('Halo ' . $notifiable->name)
            ->line("Anda telah membatalkan pendaftaran event: {$this->event->nama_event}.")
            ->action('Lihat Event', url('/event/' . $this->event->slug))
            ->line('Terima kasih.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'event_cancellation',
            'title' => 'Pendaftaran Event Dibatalkan',
            'message' => "Pendaftaran Anda di event {$this->event->nama_event} telah dibatalkan.",
            'icon' => 'alert-circle',
            'action_url' => url('/event/' . $this->event->slug),
            'action_text' => 'Lihat Event',
        ];
    }
}
