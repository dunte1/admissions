<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SystemBroadcast extends Notification implements ShouldQueue
{
    use Queueable;

    public string $title;
    public string $message;
    public ?string $type;

    public function __construct(string $title, string $message, ?string $type = 'broadcast')
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $schoolName = $notifiable->school?->name ?? config('app.name');

        return (new MailMessage)
            ->subject("[{$schoolName}] {$this->title}")
            ->greeting("Hello {$notifiable->fullName()},")
            ->line($this->message)
            ->action('View Dashboard', url('/dashboard'))
            ->line('---')
            ->line($this->type === 'broadcast' 
                ? 'This is a system broadcast notification.' 
                : "This notification is about: {$this->type}")
            ->salutation('Regards, ' . $schoolName);
    }

    public function toVonage(object $notifiable): array
    {
        $message = "{$this->title}\n\n{$this->message}";
        
        return [
            'content' => $message,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => '/dashboard',
            'icon' => 'bell',
            'color' => 'purple',
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function broadcastType(): string
    {
        return 'broadcast';
    }

    public function broadcastWith(): array
    {
        return $this->toArray(new \App\Models\User);
    }
}
