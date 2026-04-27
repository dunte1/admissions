<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentVerificationController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function show(Payment $payment)
    {
        return response()->json([
            'id' => $payment->id,
            'transaction_id' => $payment->transaction_id,
            'phone_number' => $payment->phone_number,
            'amount' => $payment->amount,
            'payment_type' => $payment->payment_type,
            'payment_method' => $payment->payment_method,
            'status' => $payment->status,
            'application' => [
                'application_number' => $payment->application->application_number,
                'student_name' => $payment->application->student->full_name ?? 'N/A',
            ],
        ]);
    }

    public function verify(Request $request, Payment $payment)
    {
        if (!$request->user()->hasPermissionTo('verify_manual_payments')) {
            abort(403, 'Unauthorized action.');
        }

        if ($payment->status !== 'manual_pending') {
            return response()->json([
                'success' => false,
                'message' => 'This payment is not pending verification.',
            ]);
        }

        $result = $this->paymentService->verifyManualPayment($payment);

        if ($result['success']) {
            Log::info('Manual payment verified', [
                'payment_id' => $payment->id,
                'application_id' => $payment->application_id,
                'amount' => $payment->amount,
                'receipt' => $result['receipt'],
                'verified_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'receipt' => $result['receipt'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to verify payment',
        ]);
    }

    public function reject(Request $request, Payment $payment)
    {
        if (!$request->user()->hasPermissionTo('verify_manual_payments')) {
            abort(403, 'Unauthorized action.');
        }

        if ($payment->status !== 'manual_pending') {
            return response()->json([
                'success' => false,
                'message' => 'This payment is not pending verification.',
            ]);
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        try {
            $payment->update([
                'status' => 'rejected',
                'failure_reason' => $request->reason,
            ]);

            \App\Models\PaymentLog::log($payment, 'rejected', 'Manual payment rejected: ' . $request->reason);

            Log::info('Manual payment rejected', [
                'payment_id' => $payment->id,
                'reason' => $request->reason,
                'rejected_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment rejected',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to reject payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject payment',
            ]);
        }
    }
}