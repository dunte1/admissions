<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Payment $payment,
        public ?string $pdfPath = null
    ) {
    }

    public function envelope(): Envelope
    {
        $schoolName = $this->payment->school?->name ?? system_setting('system_name', config('app.name'));
        $subject = "Payment Receipt - {$this->payment->receipt_number} | {$schoolName}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-receipt',
        );
    }

    public function attachments(): array
    {
        if (!$this->pdfPath || !file_exists($this->pdfPath)) {
            Log::warning('PDF attachment not found', [
                'payment_id' => $this->payment->id,
                'pdf_path' => $this->pdfPath,
            ]);
            return [];
        }

        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath($this->pdfPath)
                ->as('receipt-' . $this->payment->receipt_number . '.pdf')
                ->mime('application/pdf'),
        ];
    }
}