<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Lamaran;
use App\Models\Lowongan;

class LamaranSubmissionNotification extends Notification
{
    use Queueable;

    private $lamaran;
    private $lowongan;

    public function __construct(Lamaran $lamaran, Lowongan $lowongan)
    {
        $this->lamaran = $lamaran;
        $this->lowongan = $lowongan;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Lamaran Pekerjaan Berhasil Terkirim: ' . $this->lowongan->judul_lowongan)
            ->greeting('Halo ' . $notifiable->name)
            ->line("Lamaran Anda untuk posisi {$this->lowongan->judul_lowongan} telah berhasil terkirim.")
            ->line("Status lamaran Anda saat ini: Diproses")
            ->line("Tim kami akan segera melakukan review terhadap lamaran Anda.")
            ->action('Lihat Lowongan', url('/lowongan/' . $this->lowongan->slug))
            ->line('Terima kasih telah melamar di perusahaan kami.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'lamaran_submission',
            'title' => 'Lamaran Berhasil Terkirim',
            'message' => "Lamaran Anda untuk posisi {$this->lowongan->judul_lowongan} telah berhasil terkirim.",
            'icon' => 'briefcasebussiness',
            'action_url' => '/lowongan/' . $this->lowongan->slug,
            'action_text' => 'Lihat Lowongan',
            'id_lamaran' => $this->lamaran->id_lamaran,
            'id_lowongan' => $this->lowongan->id_lowongan,
        ];
    }
}