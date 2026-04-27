<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InfoRequested extends Notification implements ShouldQueue
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
            ->subject('Additional Information Required - ' . app_name())
            ->view('emails.application-info-requested', [
                'notifiable' => $notifiable,
                'application' => $this->application,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'info_requested',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'program' => $this->application->program->name ?? 'N/A',
            'message' => 'Additional information required for your ' . ($this->application->program->name ?? 'N/A') . ' application.',
            'request' => $this->application->review_notes,
        ];
    }
}
