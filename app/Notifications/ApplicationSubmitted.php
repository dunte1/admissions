<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Application $application)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database', 'mail'];
        
        $formData = $this->application->form_data;
        $personalData = $formData['personal'] ?? [];
        $primaryPhone = $personalData['primary_phone'] ?? $personalData['phone'] ?? null;
        
        if ($primaryPhone) {
            $channels[] = 'sms';
        }
        
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $schoolName = $this->application->school ? $this->application->school->name : app_name();
        $programName = $this->application->program ? $this->application->program->name : 'N/A';
        
        $formData = $this->application->form_data;
        $personalData = $formData['personal'] ?? [];
        $applicantEmail = $personalData['primary_email'] ?? $notifiable->email;

        return (new MailMessage)
            ->subject("[{$schoolName}] Application Received - #{$this->application->application_number}")
            ->greeting("Hello {$notifiable->fullName()},")
            ->line("Thank you for submitting your application!")
            ->line("**Application Number:** {$this->application->application_number}")
            ->line("**Program:** {$programName}")
            ->line("We have received your application and will review it shortly.")
            ->action('View Application', url('/student/applications/' . $this->application->id))
            ->line("You will receive updates about your application status via this email ({$applicantEmail}).")
            ->salutation("Regards, {$schoolName}");
    }
    
    public function toSms($notifiable): string
    {
        $schoolName = $this->application->school ? $this->application->school->name : app_name();
        $formData = $this->application->form_data;
        $personalData = $formData['personal'] ?? [];
        $primaryPhone = $personalData['primary_phone'] ?? $personalData['phone'] ?? '';
        
        return "Dear Applicant, Your application #{$this->application->application_number} to {$schoolName} has been received. You will be notified of the outcome via email. - {$schoolName}";
    }

    public function toArray(object $notifiable): array
    {
        $programName = $this->application->program ? $this->application->program->name : 'N/A';
        
        return [
            'type' => 'application_submitted',
            'application_id' => $this->application->id,
            'application_number' => $this->application->application_number,
            'program' => $programName,
            'message' => 'Your application for ' . $programName . ' has been submitted successfully.',
            'icon' => 'check-circle',
            'color' => 'green',
        ];
    }
}
