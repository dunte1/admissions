<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewApplicationReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $student = $this->application->student;
        $applicantName = $student ? $student->first_name . ' ' . $student->last_name : 'Unknown Applicant';

        return (new MailMessage)
            ->subject('New Application Received - ' . $this->application->application_number)
            ->greeting('Hello ' . $notifiable->first_name)
            ->line('A new application has been submitted.')
            ->line('**Applicant:** ' . $applicantName)
            ->line('**Application Number:** ' . $this->application->application_number)
            ->line('**Program:** ' . ($this->application->program->name ?? 'N/A'))
            ->action('Review Application', url('/admin/applications/' . $this->application->id));
    }

    public function toArray(object $notifiable): array
    {
        $applicant = $this->application->user;
        $student = $this->application->student;
        
        return [
            'type' => 'new_application',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'program' => $this->application->program->name ?? 'N/A',
            'applicant_name' => $student ? $student->first_name . ' ' . $student->last_name : ($applicant->fullName() ?? 'N/A'),
            'message' => 'New application received from ' . ($student ? $student->first_name . ' ' . $student->last_name : 'Unknown') . ' for ' . ($this->application->program->name ?? 'N/A'),
            'icon' => 'document-text',
            'color' => 'blue',
        ];
    }
}
