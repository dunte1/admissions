<?php

namespace App\Notifications\Subscription;

use App\Models\School;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionPaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public School $school,
        public Subscription $subscription,
        public Payment $payment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->subscription->plan;
        $receipt = $this->payment->mpesa_receipt ?? $this->payment->transaction_id ?? 'N/A';

        return (new MailMessage)
            ->subject("Payment Received - {$this->school->name}")
            ->greeting("Hello {$this->school->getPrimaryContactName()}!")
            ->line("We have received your subscription payment!")
            ->line("**Amount:** {$this->school->currency_symbol}" . number_format($this->payment->amount, 2))
            ->line("**Plan:** {$plan->name}")
            ->line("**Payment Reference:** {$receipt}")
            ->line("**Payment Method:** " . ucfirst($this->payment->payment_method))
            ->line("**Next Renewal:** {$this->subscription->expires_at->format('M d, Y')}")
            ->action('View Subscription', url('/admin/subscription'))
            ->line('Thank you for your continued business!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'school_id' => $this->school->id,
            'school_name' => $this->school->name,
            'subscription_id' => $this->subscription->id,
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'type' => 'subscription_payment_received',
            'message' => "Payment of {$this->school->currency_symbol}" . number_format($this->payment->amount, 2) . " received. Thank you!",
        ];
    }
}
