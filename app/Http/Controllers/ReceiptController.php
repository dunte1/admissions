<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function studentDownload(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this receipt.');
        }

        if (!$payment->receipt_number || $payment->status !== 'completed') {
            abort(404, 'Receipt not found or payment not completed.');
        }

        return $this->generatePdf($payment, 'student');
    }

    public function adminDownload(Payment $payment)
    {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['super_admin', 'admin', 'registrar', 'accountant'])) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->hasRole('admin') && $payment->school_id !== $user->school_id) {
            abort(403, 'You can only view receipts for your school.');
        }

        if (!$payment->receipt_number) {
            abort(404, 'Receipt not found.');
        }

        return $this->generatePdf($payment, 'admin');
    }

    protected function generatePdf(Payment $payment, string $type)
    {
        $payment->load(['application.user', 'application.intake', 'school']);
        
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

        $filename = 'receipt-' . $payment->receipt_number . '.pdf';

        Log::info('Receipt generated', [
            'payment_id' => $payment->id,
            'receipt_number' => $payment->receipt_number,
            'generated_by' => $type,
            'user_id' => Auth::id(),
        ]);

        return $pdf->download($filename);
    }
}