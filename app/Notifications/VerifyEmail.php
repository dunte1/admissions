<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $appName = system_setting('app_name', config('app.name'));
        $supportEmail = system_setting('contact_email', config('mail.from.address', 'support@example.com'));

        return (new MailMessage)
            ->subject("Verify Your Email - {$appName}")
            ->greeting("Hello {$notifiable->first_name}!")
            ->line('Thank you for registering with ' . $appName . '.')
            ->line('Please verify your email address to activate your account.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('This link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->line("If you need assistance, contact us at {$supportEmail}")
            ->salutation('Best regards, ' . $appName . ' Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'email_verification',
            'message' => 'Please verify your email address.',
        ];
    }
}