<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpired extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public School $school,
        public int $graceDays
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Subscription Expired - {$this->school->name}")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("Your {$this->school->name} subscription has expired.")
            ->line("**Grace Period:** {$this->graceDays} days")
            ->line("You have until {$this->school->grace_ends_at->format('M d, Y')} to renew your subscription.")
            ->line('During this time, you have read-only access. Subscribe to restore full functionality.')
            ->action('Renew Now', url('/admin/subscription'))
            ->line('Questions? Contact our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'grace_days' => $this->graceDays,
            'grace_ends_at' => $this->school->grace_ends_at->toDateTimeString(),
            'type' => 'subscription_expired',
            'message' => "Your subscription has expired. Grace period: {$this->graceDays} days.",
        ];
    }
}
