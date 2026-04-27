<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendOTP extends Notification
{
    use Queueable;

    public string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Login OTP Code')
            ->greeting('Hello!')
            ->line('Your one-time password (OTP) for login is:')
            ->line("**{$this->otp}**")
            ->line('This code will expire in 15 minutes.')
            ->line('If you did not request this OTP, please ignore this email or contact support.');
    }
}
