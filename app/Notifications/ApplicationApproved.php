<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Congratulations! Your Application Has Been Approved - ' . app_name())
            ->view('emails.application-approved', [
                'notifiable' => $notifiable,
                'application' => $this->application,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'application_approved',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'program' => $this->application->program->name ?? 'N/A',
            'message' => 'Your application for ' . ($this->application->program->name ?? 'N/A') . ' has been approved.',
        ];
    }
}
