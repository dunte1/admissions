<?php

namespace App\Mail;

use App\Models\AdmissionLetter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionLetterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AdmissionLetter $letter)
    {
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->letter->type) {
            'admission' => 'Admission Offer - ' . $this->letter->letter_number,
            'rejection' => 'Application Update - ' . $this->letter->letter_number,
            'provisional' => 'Provisional Offer - ' . $this->letter->letter_number,
            default => 'Official Letter - ' . $this->letter->letter_number,
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-letter',
        );
    }
}
