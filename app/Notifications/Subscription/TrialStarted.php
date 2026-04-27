<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialStarted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public School $school,
        public int $trialDays
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your {$this->school->name} Trial Has Started")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("Great news! Your {$this->school->name} admission portal trial has officially started.")
            ->line("**Duration:** {$this->trialDays} days")
            ->line("**Trial Ends:** {$this->school->trial_ends_at->format('M d, Y')}")
            ->line('You now have full access to all features to explore the platform.')
            ->action('Go to Dashboard', url('/admin/dashboard'))
            ->line('Need help getting started? Check our documentation or contact support.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'trial_days' => $this->trialDays,
            'trial_ends_at' => $this->school->trial_ends_at->toDateTimeString(),
            'type' => 'trial_started',
            'message' => "Your {$this->school->name} trial has started. Ends on {$this->school->trial_ends_at->format('M d, Y')}.",
        ];
    }
}
