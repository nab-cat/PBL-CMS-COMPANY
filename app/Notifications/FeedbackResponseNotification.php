<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Feedback;

class FeedbackResponseNotification extends Notification
{
    use Queueable;

    public $feedback;
    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'feedback_response',
            'title' => 'Feedback Anda Telah Ditanggapi',
            'message' => "Feedback Anda dengan subjek \"{$this->feedback->subjek_feedback}\" telah mendapat tanggapan: \"{$this->feedback->tanggapan_feedback}\"",
            'icon' => 'chat-bubble-left-right',
            'id_feedback' => $this->feedback->id_feedback,
            'subjek_feedback' => $this->feedback->subjek_feedback,
            'tanggapan_feedback' => $this->feedback->tanggapan_feedback,
            'tingkat_kepuasan' => $this->feedback->tingkat_kepuasan,
        ];
    }
}
