<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to users 3 days before events start';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for events that need reminders...');

        // Get events that start in exactly 3 days
        $reminderDate = Carbon::now()->addDays(3)->startOfDay();
        $reminderDateEnd = Carbon::now()->addDays(3)->endOfDay();

        $events = Event::with('users')
            ->whereBetween('waktu_start_event', [$reminderDate, $reminderDateEnd])
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events found that need reminders today.');
            return;
        }

        $totalNotifications = 0;

        foreach ($events as $event) {
            $this->info("Processing event: {$event->nama_event}");

            foreach ($event->users as $user) {
                try {
                    $user->notify(new EventReminderNotification($event));
                    $totalNotifications++;
                    $this->line("  - Reminder sent to: {$user->email}");
                } catch (\Exception $e) {
                    $this->error("  - Failed to send reminder to {$user->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Reminder process completed. Total notifications sent: {$totalNotifications}");
    }
}
