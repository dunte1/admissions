<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountSuspended extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public School $school,
        public string $reason = 'Subscription payment was not received.'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Account Suspended - {$this->school->name}")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("Your {$this->school->name} admission portal account has been **suspended**.")
            ->line("**Reason:** {$this->reason}")
            ->line('You can no longer access the platform. All your data has been preserved.')
            ->line('To restore your account, please contact our support team.')
            ->line('We are here to help you get back on track.')
            ->action('Contact Support', url('/contact'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'reason' => $this->reason,
            'type' => 'account_suspended',
            'priority' => 'high',
            'message' => "Your account has been suspended. Reason: {$this->reason}",
        ];
    }
}
