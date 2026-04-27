<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EscalationNotification extends Notification
{
    use Queueable;

    protected $conversation;

    public function __construct($conversation)
    {
        $this->conversation = $conversation;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('AI Escalation - User Needs Human Support')
            ->line('A user has requested human assistance through the AI chat.')
            ->line('Conversation ID: ' . $this->conversation->id)
            ->line('Initial Message: ' . $this->conversation->initial_message)
            ->action('View Conversation', route('admin.conversations.show', $this->conversation->id))
            ->line('Please respond to this user as soon as possible.');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'AI Escalation',
            'message' => 'User requested human assistance',
            'conversation_id' => $this->conversation->id,
            'url' => route('admin.conversations.show', $this->conversation->id),
        ];
    }
}
