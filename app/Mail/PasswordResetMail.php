<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $newPassword
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Password Has Been Reset - ' . system_setting('system_name', 'Admission Portal'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'user' => $this->user,
                'newPassword' => $this->newPassword,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
