<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
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
        $eventDate = $this->event->waktu_start_event->format('d M Y');
        $eventTime = $this->event->waktu_start_event->format('H:i');

        return (new MailMessage)
            ->subject('Pengingat Event: ' . $this->event->nama_event . ' - 3 Hari Lagi!')
            ->greeting('Halo ' . $notifiable->name)
            ->line("Event {$this->event->nama_event} akan dimulai dalam 3 hari!")
            ->line("Tanggal: {$eventDate}")
            ->line("Waktu: {$eventTime}")
            ->line("Lokasi: {$this->event->lokasi_event}")
            ->action('Lihat Detail Event', url('/event/' . $this->event->slug))
            ->line('Jangan sampai terlewat! Siapkan diri Anda untuk event ini.');
    }

    public function toArray($notifiable)
    {
        $eventDate = $this->event->waktu_start_event->format('d M Y');

        return [
            'type' => 'event_reminder',
            'title' => 'Pengingat Event - 3 Hari Lagi!',
            'message' => "Event {$this->event->nama_event} akan dimulai pada {$eventDate}. Jangan sampai terlewat!",
            'icon' => 'clock',
            'action_url' => url('/event/' . $this->event->slug),
            'action_text' => 'Lihat Event',
        ];
    }
}
