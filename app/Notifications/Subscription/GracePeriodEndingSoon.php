<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GracePeriodEndingSoon extends Notification implements ShouldQueue
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
            ->subject("Urgent: Grace Period Ending - {$this->school->name}")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("⚠️ **URGENT:** Your {$this->school->name} grace period ends in **{$this->daysRemaining} days**.")
            ->line("After {$this->school->grace_ends_at->format('M d, Y')}, your account will be suspended.")
            ->line('Suspended accounts cannot be accessed. All data will be preserved but you will not be able to use the platform.')
            ->action('Renew NOW', url('/admin/subscription'))
            ->line('Contact us immediately if you need assistance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'days_remaining' => $this->daysRemaining,
            'grace_ends_at' => $this->school->grace_ends_at->toDateTimeString(),
            'type' => 'grace_period_ending_soon',
            'priority' => 'high',
            'message' => "⚠️ Grace period ends in {$this->daysRemaining} days. Renew now to avoid suspension!",
        ];
    }
}
