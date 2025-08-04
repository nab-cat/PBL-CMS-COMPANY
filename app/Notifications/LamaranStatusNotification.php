<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Lamaran;
use App\Models\Lowongan;

class LamaranStatusNotification extends Notification
{
    use Queueable;

    private $lamaran;
    private $lowongan;
    private $oldStatus;
    private $newStatus;

    public function __construct(Lamaran $lamaran, Lowongan $lowongan, $oldStatus, $newStatus)
    {
        $this->lamaran = $lamaran;
        $this->lowongan = $lowongan;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $subject = 'Status Lamaran Diperbarui: ' . $this->lowongan->judul_lowongan;
        $statusMessage = $this->getStatusMessage();
        
        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Halo ' . $notifiable->name)
            ->line("Status lamaran Anda untuk posisi {$this->lowongan->judul_lowongan} telah diperbarui.")
            ->line("Status sekarang: {$this->newStatus}");
            
        // Tambahkan pesan sesuai status
        foreach ($statusMessage as $line) {
            $mail->line($line);
        }
            
        return $mail->action('Lihat Lowongan', url('/lowongan/' . $this->lowongan->slug))
            ->line('Terima kasih telah melamar di perusahaan kami.');
    }

    public function toArray($notifiable)
    {
        $type = 'lamaran_' . strtolower($this->newStatus);
        $title = 'Lamaran ' . $this->newStatus;
        $message = "Status lamaran Anda untuk posisi {$this->lowongan->judul_lowongan} telah diperbarui menjadi {$this->newStatus}.";
        $icon = $this->newStatus === 'Diterima' ? 'check-circle' : 'x-circle';
        
        if ($this->newStatus === 'Diterima') {
            $type = 'lamaran_diterima';
            $title = 'Selamat! Lamaran Diterima';
        } elseif ($this->newStatus === 'Ditolak') {
            $type = 'lamaran_ditolak'; 
            $title = 'Lamaran Ditolak';
        }
        
        return [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'action_url' => '/lowongan/' . $this->lowongan->slug,
            'action_text' => 'Lihat Detail',
            'id_lamaran' => $this->lamaran->id_lamaran,
            'id_lowongan' => $this->lowongan->id_lowongan,
        ];
    }
    
    private function getStatusMessage()
    {
        switch ($this->newStatus) {
            case 'Diterima':
                return [
                    'Selamat! Lamaran Anda telah diterima.',
                    'Tim rekrutmen kami akan menghubungi Anda melalui email atau telepon untuk langkah selanjutnya.'
                ];
            case 'Ditolak':
                return [
                    'Mohon maaf, lamaran Anda belum dapat kami terima saat ini.',
                    'Jangan berkecil hati, kami tetap menghargai ketertarikan Anda terhadap perusahaan kami.'
                ];
            default:
                return ['Tim kami sedang meninjau lamaran Anda.'];
        }
    }
}