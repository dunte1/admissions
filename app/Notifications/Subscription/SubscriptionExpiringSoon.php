<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringSoon extends Notification implements ShouldQueue
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
            ->subject("Subscription Renewing Soon - {$this->school->name}")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("Your {$this->school->name} subscription renews in **{$this->daysRemaining} days**.")
            ->line('Make sure your payment method is up to date to avoid any interruption in service.')
            ->action('Manage Subscription', url('/admin/subscription'))
            ->line('Questions? Contact our billing team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'days_remaining' => $this->daysRemaining,
            'type' => 'subscription_expiring_soon',
            'message' => "Your subscription renews in {$this->daysRemaining} days.",
        ];
    }
}
