<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public School $school,
        public Subscription $subscription
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->subscription->plan;
        $endsAt = $this->subscription->expires_at->format('M d, Y');

        return (new MailMessage)
            ->subject("Subscription Activated - {$this->school->name}")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("Your {$this->school->name} subscription has been activated!")
            ->line("**Plan:** {$plan->name}")
            ->line("**Status:** Active")
            ->line("**Renews:** {$endsAt}")
            ->action('Go to Dashboard', url('/admin/dashboard'))
            ->line('Thank you for choosing our platform!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'subscription_id' => $this->subscription->id,
            'plan_name' => $this->subscription->plan?->name,
            'expires_at' => $this->subscription->expires_at->toDateTimeString(),
            'type' => 'subscription_activated',
            'message' => "Your {$this->subscription->plan?->name} subscription is now active!",
        ];
    }
}
