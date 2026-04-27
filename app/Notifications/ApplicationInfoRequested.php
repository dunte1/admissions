<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationInfoRequested extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $schoolName = $this->application->school ? $this->application->school->name : app_name();
        $programName = $this->application->program ? $this->application->program->name : 'N/A';

        return (new MailMessage)
            ->subject("[{$schoolName}] Additional Information Required - #{$this->application->application_number}")
            ->greeting("Hello {$notifiable->fullName()},")
            ->line("We need additional information for your application.")
            ->line("**Application Number:** {$this->application->application_number}")
            ->line("**Program:** {$programName}")
            ->line("Please log in to your portal to provide the requested information.")
            ->action('Provide Information', url('/student/applications/' . $this->application->id))
            ->line("Failure to provide the requested information may affect your application processing time.")
            ->salutation("Regards, {$schoolName}");
    }

    public function toArray(object $notifiable): array
    {
        $programName = $this->application->program ? $this->application->program->name : 'N/A';
        
        return [
            'type' => 'info_requested',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'program' => $programName,
            'message' => 'Additional information is required for your application for ' . $programName . '.',
            'icon' => 'exclamation-circle',
            'color' => 'yellow',
        ];
    }
}
