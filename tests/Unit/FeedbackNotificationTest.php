<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FeedbackResponseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeedbackNotificationTest extends TestCase
{
    use RefreshDatabase;    /** @test */
    public function it_sends_notification_when_feedback_response_is_added()
    {
        Notification::fake();

        // Create a user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        // Create feedback without response
        $feedback = Feedback::create([
            'id_user' => $user->id_user,
            'subjek_feedback' => 'Test Feedback',
            'tingkat_kepuasan' => 5,
            'isi_feedback' => 'This is a test feedback',
            'status_feedback' => 'tidak terpublikasi',
        ]);

        // Update feedback with response (this should trigger notification)
        $feedback->update([
            'tanggapan_feedback' => 'Thank you for your feedback!'
        ]);        // Assert notification was sent
        Notification::assertSentTo(
            $user,
            FeedbackResponseNotification::class,
            function ($notification, $channels, $notifiable) use ($feedback) {
                $notificationData = $notification->toArray($notifiable);
                return $notification->feedback->id_feedback === $feedback->id_feedback &&
                    str_contains($notificationData['message'], 'Thank you for your feedback!');
            }
        );
    }

    /** @test */
    public function it_does_not_send_notification_when_feedback_is_created_with_response()
    {
        Notification::fake();

        $user = User::factory()->create();

        // Create feedback with response already set
        Feedback::create([
            'id_user' => $user->id_user,
            'subjek_feedback' => 'Test Feedback',
            'tingkat_kepuasan' => 5,
            'isi_feedback' => 'This is a test feedback',
            'tanggapan_feedback' => 'Pre-existing response',
            'status_feedback' => 'tidak terpublikasi',
        ]);

        // Assert no notification was sent
        Notification::assertNothingSent();
    }

    /** @test */
    public function it_does_not_send_notification_when_other_fields_are_updated()
    {
        Notification::fake();

        $user = User::factory()->create();

        $feedback = Feedback::create([
            'id_user' => $user->id_user,
            'subjek_feedback' => 'Test Feedback',
            'tingkat_kepuasan' => 5,
            'isi_feedback' => 'This is a test feedback',
            'status_feedback' => 'tidak terpublikasi',
        ]);

        // Update other fields (should not trigger notification)
        $feedback->update([
            'status_feedback' => 'terpublikasi'
        ]);

        // Assert no notification was sent
        Notification::assertNothingSent();
    }
}
