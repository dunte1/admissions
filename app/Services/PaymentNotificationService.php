<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentLog;
use App\Mail\PaymentReceiptMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentNotificationService
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function sendAllNotifications(Payment $payment): array
    {
        $results = [
            'receipt_generated' => false,
            'email_sent' => false,
            'whatsapp_sent' => false,
            'notification_created' => false,
        ];

        try {
            $payment->load(['application.user', 'application.intake', 'school']);

            $receiptNumber = $this->generateReceiptNumber($payment);
            $results['receipt_number'] = $receiptNumber;
            Log::info('Starting payment notification flow', [
                'payment_id' => $payment->id,
                'receipt_number' => $receiptNumber,
            ]);

            $pdfPath = $this->generateAndStorePdf($payment);
            if ($pdfPath) {
                $results['receipt_generated'] = true;
                Log::info('PDF generated', ['path' => $pdfPath]);
            }

            $emailResult = $this->sendEmail($payment, $pdfPath);
            $results['email_sent'] = $emailResult['success'];
            $results['email_message'] = $emailResult['message'];
            Log::info('Email notification result', $emailResult);

            $whatsappResult = $this->sendWhatsApp($payment);
            $results['whatsapp_sent'] = $whatsappResult['success'];
            $results['whatsapp_message'] = $whatsappResult['message'];
            Log::info('WhatsApp notification result', $whatsappResult);

            $notificationResult = $this->createInAppNotification($payment);
            $results['notification_created'] = $notificationResult['success'];
            Log::info('In-app notification result', $notificationResult);

            $payment->logEvent('notifications_sent', 'All notifications processed', $results);

            return $results;
        } catch (\Exception $e) {
            Log::error('Notification flow error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $payment->logEvent('notifications_failed', 'Notification flow failed: ' . $e->getMessage());

            return $results;
        }
    }

    protected function generateReceiptNumber(Payment $payment): string
    {
        if ($payment->receipt_number) {
            return $payment->receipt_number;
        }

        return $payment->generateReceiptNumber();
    }

    protected function generateAndStorePdf(Payment $payment): ?string
    {
        try {
            $application = $payment->application;
            $branding = $payment->getSchoolBranding();
            $systemName = system_setting('system_name', 'Admission Portal');

            $pdf = Pdf::loadView('student.exports.payment-receipt', [
                'payment' => $payment,
                'application' => $application,
                'branding' => $branding,
                'system_name' => $systemName,
            ]);

            $pdf->setPaper('A4');

            $filename = 'receipts/' . $payment->receipt_number . '.pdf';
            $path = storage_path('app/' . $filename);

            $directory = dirname($path);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($path, $pdf->output());

            return $path;
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function sendEmail(Payment $payment, ?string $pdfPath): array
    {
        try {
            $user = $payment->user;
            
            if (!$user || !$user->email) {
                Log::warning('No email address for payment notification', [
                    'payment_id' => $payment->id,
                    'user_id' => $payment->user_id,
                ]);
                return ['success' => false, 'message' => 'No email address'];
            }

            \Mail::to($user->email)->send(new PaymentReceiptMail($payment, $pdfPath));

            PaymentLog::create([
                'payment_id' => $payment->id,
                'school_id' => $payment->school_id,
                'user_id' => $payment->user_id,
                'event' => 'email_sent',
                'description' => 'Payment receipt email sent',
                'metadata' => ['to' => $user->email],
            ]);

            return ['success' => true, 'message' => 'Email sent successfully'];
        } catch (\Exception $e) {
            Log::error('Email send failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function sendWhatsApp(Payment $payment): array
    {
        return $this->whatsAppService->sendPaymentConfirmation($payment);
    }

    protected function createInAppNotification(Payment $payment): array
    {
        try {
            $application = $payment->application;
            
            $feeType = $payment->payment_type === 'commitment_fee' ? 'Commitment Fee' : 'Admission Fee';
            $title = $payment->payment_type === 'commitment_fee' ? 'Commitment Fee Payment Confirmed' : 'Payment Confirmed';
            $message = $payment->payment_type === 'commitment_fee' 
                ? "Your commitment fee payment of {$payment->formatted_amount} has been confirmed. Your place is now secured! Receipt: {$payment->receipt_number}"
                : "Your payment of {$payment->formatted_amount} has been confirmed. Receipt: {$payment->receipt_number}";

            $notification = \App\Models\Notification::create([
                'user_id' => $payment->user_id,
                'school_id' => $payment->school_id,
                'type' => 'payment_received',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'payment_id' => $payment->id,
                    'application_id' => $payment->application_id,
                    'payment_type' => $payment->payment_type,
                    'amount' => $payment->amount,
                    'receipt_number' => $payment->receipt_number,
                ],
                'is_read' => false,
            ]);

            if ($notification) {
                PaymentLog::create([
                    'payment_id' => $payment->id,
                    'school_id' => $payment->school_id,
                    'user_id' => $payment->user_id,
                    'event' => 'notification_created',
                    'description' => 'In-app notification created',
                    'metadata' => ['notification_id' => $notification->id],
                ]);
            }

            return ['success' => true, 'message' => 'Notification created'];
        } catch (\Exception $e) {
            Log::error('In-app notification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    public function sendManualPaymentNotification(Payment $payment): array
    {
        try {
            $admins = \App\Models\User::where('school_id', $payment->school_id)
                ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'accountant']))
                ->get();
                
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'school_id' => $payment->school_id,
                    'type' => 'manual_payment_pending',
                    'title' => 'Manual Payment Requires Verification',
                    'message' => "A manual payment of KES " . number_format($payment->amount) . " for application {$payment->application->application_number} requires verification.",
                    'data' => [
                        'payment_id' => $payment->id,
                        'application_id' => $payment->application_id,
                        'amount' => $payment->amount,
                    ],
                    'is_read' => false,
                ]);
            }
            
            return ['success' => true, 'message' => 'Admin notification sent'];
        } catch (\Exception $e) {
            Log::error('Admin notification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}