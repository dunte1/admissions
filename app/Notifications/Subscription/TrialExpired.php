<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialExpired extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public School $school) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Trial Expired - {$this->school->name}")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("Your {$this->school->name} admission portal trial has expired.")
            ->line("Your account is now in read-only mode. You can view existing data but cannot create new applications.")
            ->line('Subscribe to a plan to restore full functionality.')
            ->action('Subscribe Now', url('/admin/subscription'))
            ->line('Questions? Contact our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'type' => 'trial_expired',
            'message' => "Your trial has expired. Subscribe to continue using the platform.",
        ];
    }
}
