<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialEndingSoon extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public School $school,
        public int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Trial Ending Soon - {$this->school->name}")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("Your {$this->school->name} admission portal trial is ending in **{$this->daysRemaining} days**.")
            ->line("**Trial Ends:** {$this->school->trial_ends_at->format('M d, Y')}")
            ->line("Don't lose access to your applications and data. Subscribe to a plan before your trial ends.")
            ->action('Subscribe Now', url('/admin/subscription'))
            ->line('Questions? Contact our sales team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'days_remaining' => $this->daysRemaining,
            'trial_ends_at' => $this->school->trial_ends_at->toDateTimeString(),
            'type' => 'trial_ending_soon',
            'message' => "Your trial ends in {$this->daysRemaining} days. Subscribe now!",
        ];
    }
}
