<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUser extends Notification implements ShouldQueue
{
    use Queueable;

    protected ?string $password;

    public function __construct(?string $password = null)
    {
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = system_setting('app_name', config('app.name'));
        $supportEmail = system_setting('contact_email', config('mail.from.address', 'support@example.com'));

        $mail = (new MailMessage)
            ->subject("Welcome to {$appName} - Account Created")
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line("Welcome to {$appName}! Your account has been successfully created.")
            ->line('Here are your account details:')
            ->line('**Email:** ' . $notifiable->email)
            ->action('Login to Portal', url('/login'))
            ->line('Please login to start your application process.');

        if ($this->password) {
            $mail->line('**Temporary Password:** ' . $this->password);
            $mail->line('**Please change your password after your first login.**');
        }

        $mail->line("If you have any questions, please contact our support team at {$supportEmail}.")
            ->salutation('Best regards, ' . $appName . ' Team');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $appName = system_setting('app_name', config('app.name'));
        return [
            'type' => 'welcome',
            'message' => "Welcome to {$appName}! Your account has been created.",
        ];
    }
}
