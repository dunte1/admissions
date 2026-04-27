<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $application;

    public function __construct($payment, $application)
    {
        $this->payment = $payment;
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Received - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('We have received your payment successfully.')
            ->line('**Payment Details:**')
            ->line('- **Reference:** ' . $this->payment->reference)
            ->line('- **Amount:** KES ' . number_format($this->payment->amount, 2))
            ->line('- **Application:** ' . $this->application->application_number)
            ->line('- **Date:** ' . $this->payment->paid_at->format('F j, Y'))
            ->line('Please retain this email as your payment confirmation.')
            ->action('View Application', url('/student/application/' . $this->application->id))
            ->salutation('Best regards, ' . config('app.name') . ' Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_received',
            'payment_id' => $this->payment->id,
            'application_id' => $this->application->id,
            'amount' => $this->payment->amount,
            'reference' => $this->payment->reference,
            'message' => 'Payment of KES ' . number_format($this->payment->amount, 2) . ' received for application ' . $this->application->application_number,
        ];
    }
}
