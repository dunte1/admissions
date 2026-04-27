<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function show(Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        $payments = $application->payments()->latest()->get();
        $admissionPayment = $payments->where('payment_type', 'admission_fee')->first();
        $commitmentPayment = $payments->where('payment_type', 'commitment_fee')->first();
        $pendingPayment = $payments->where('status', 'pending')->first();
        
        $paymentConfig = $this->paymentService->getPaymentConfig($application);
        
        $showCommitmentFee = in_array($application->status, ['offered', 'accepted']);
        $commitmentDueDate = null;
        
        if ($showCommitmentFee) {
            $admissionLetter = $application->admissionLetter()->where('type', 'admission')->latest()->first();
            if ($admissionLetter) {
                $dueDays = $paymentConfig['commitment_fee']['due_days'] ?? 14;
                $commitmentDueDate = $admissionLetter->created_at->addDays($dueDays);
            }
        }

        return view('student.payment', compact(
            'application', 
            'payments', 
            'admissionPayment',
            'commitmentPayment',
            'pendingPayment', 
            'paymentConfig',
            'showCommitmentFee',
            'commitmentDueDate'
        ));
    }

    public function initiate(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_method' => 'required|in:mpesa,paypal',
            'payment_type' => 'sometimes|in:admission_fee,commitment_fee',
            'phone' => 'required_if:payment_method,mpesa|nullable|regex:/^254[0-9]{9}$/',
        ], [
            'phone.regex' => 'Phone number must be in format 254XXXXXXXXX',
        ]);

        $paymentType = $request->input('payment_type', 'admission_fee');

        if (!in_array($application->status, ['draft', 'pending']) && $paymentType === 'admission_fee') {
            return back()->with('error', 'Application cannot accept payments in current status.');
        }

        if ($paymentType === 'commitment_fee') {
            if (!in_array($application->status, ['offered', 'accepted'])) {
                return back()->with('error', 'Commitment fee is only available after receiving an admission offer.');
            }

            $existingCommitmentPayment = $application->payments()
                ->where('payment_type', 'commitment_fee')
                ->whereIn('status', ['completed', 'manual_pending'])
                ->first();

            if ($existingCommitmentPayment) {
                return back()->with('error', 'Commitment fee has already been paid.');
            }
        }

        if ($paymentType === 'admission_fee') {
            $existingAdmissionPayment = $application->payments()
                ->where('payment_type', 'admission_fee')
                ->whereIn('status', ['completed', 'manual_pending'])
                ->first();

            if ($existingAdmissionPayment) {
                return back()->with('error', 'Admission fee has already been paid.');
            }
        }

        $existingPending = $application->payments()->where('status', 'pending')->first();
        if ($existingPending) {
            return back()->with('error', 'A payment is already pending for this application.');
        }

        $result = match($request->payment_method) {
            'mpesa' => $this->paymentService->initiateMpesa($application, $request->phone, $paymentType),
            'paypal' => $this->paymentService->initiatePayPal($application),
            default => ['success' => false, 'message' => 'Invalid payment method'],
        };

        if ($result['success']) {
            return redirect()->back()->with([
                'success' => $result['message'] ?? 'Payment request sent to your phone. Please check your phone and enter your M-PESA PIN.',
                'waiting_payment' => true,
            ]);
        }

        return redirect()->back()->with('error', $result['message'] ?? 'Payment failed');
    }

    public function submitManual(Request $request, Application $application)
    {
        if ($application->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'transaction_code' => 'required|string|regex:/^[A-Z0-9]{10,}$/|min:10',
            'phone' => 'required|regex:/^254[0-9]{9}$/',
            'amount' => 'nullable|numeric|min:1',
        ], [
            'transaction_code.regex' => 'Transaction code must be uppercase alphanumeric (e.g., QHK71XXXXX)',
            'transaction_code.min' => 'Transaction code must be at least 10 characters',
        ]);

        $paymentConfig = $this->paymentService->getPaymentConfig($application);
        
        if (!$paymentConfig['mpesa']['manual_enabled']) {
            return back()->with('error', 'Manual payment is not enabled for this school.');
        }

        $result = $this->paymentService->createManualPayment(
            $application,
            strtoupper($request->transaction_code),
            $request->phone,
            $request->amount
        );

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function callback(Request $request, Application $application)
    {
        try {
            $callbackData = $request->all();
            Log::info('MPESA Callback received', ['data' => $callbackData]);

            $result = $this->paymentService->processCallback($callbackData);

            if ($result['success']) {
                $payment = $application->payments()->where('status', 'completed')->latest()->first();
                
                return view('student.payment-success', [
                    'application' => $application,
                    'receipt' => $result['receipt'] ?? null,
                    'payment' => $payment,
                ]);
            }

            return view('student.payment-failed', [
                'application' => $application,
                'message' => $result['message'] ?? 'Payment failed',
            ]);
        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            return view('student.payment-failed', [
                'application' => $application,
                'message' => 'An error occurred processing your payment',
            ]);
        }
    }

    public function timeout(Application $application)
    {
        return view('student.payment-failed', [
            'application' => $application,
            'message' => 'Payment request timed out. Please try again.',
        ]);
    }

    public function checkStatus(Application $application)
    {
        $pendingPayment = $application->payments()->where('status', 'pending')->first();

        if (!$pendingPayment) {
            return response()->json(['status' => 'none', 'message' => 'No pending payment']);
        }

        if ($pendingPayment->payment_method === 'mpesa') {
            $result = $this->paymentService->verifyMpesaPayment($pendingPayment->transaction_id);

            if (isset($result['ResultCode']) && $result['ResultCode'] === 0) {
                $items = $result['CallbackMetadata']['Item'] ?? [];
                $mpesaData = [];
                foreach ($items as $item) {
                    $mpesaData[$item['Name']] = $item['Value'] ?? null;
                }

                $this->paymentService->confirmPayment($pendingPayment, $mpesaData);
                $pendingPayment->refresh();

                return response()->json([
                    'status' => 'completed',
                    'message' => 'Payment successful',
                    'receipt' => $mpesaData['MpesaReceiptNumber'] ?? null,
                    'receipt_number' => $pendingPayment->receipt_number,
                    'payment_id' => $pendingPayment->id,
                ]);
            } elseif (isset($result['ResultCode'])) {
                $this->paymentService->failPayment($pendingPayment, $result['ResultDesc'] ?? 'Payment failed');

                return response()->json([
                    'status' => 'failed',
                    'message' => $result['ResultDesc'] ?? 'Payment failed',
                ]);
            }
        }

        return response()->json([
            'status' => 'pending',
            'message' => 'Waiting for payment confirmation...',
        ]);
    }

    public function mpesaCallback(Request $request)
    {
        $callbackData = $request->all();
        
        Log::info('M-PESA STK Callback Received', [
            'full_data' => $callbackData,
            'ip' => $request->ip(),
        ]);

        $result = $callbackData['Body']['stkCallback'] ?? null;

        if (!$result) {
            Log::error('Invalid M-PESA callback - no stkCallback', ['data' => $callbackData]);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback data']);
        }

        $checkoutRequestId = $result['CheckoutRequestID'] ?? '';
        $resultCode = $result['ResultCode'] ?? -1;
        $resultDesc = $result['ResultDesc'] ?? '';

        Log::info('M-PESA Callback Processing', [
            'checkout_request_id' => $checkoutRequestId,
            'result_code' => $resultCode,
            'result_desc' => $resultDesc,
        ]);

        $payment = Payment::where('transaction_id', $checkoutRequestId)->first();

        if (!$payment) {
            Log::error('Payment not found for checkout ID', ['checkout_id' => $checkoutRequestId]);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Payment not found']);
        }

        $payment->update(['callback_received_at' => now()]);

        if ($resultCode === 0) {
            $items = $result['CallbackMetadata']['Item'] ?? [];
            $mpesaData = [];

            foreach ($items as $item) {
                $name = $item['Name'] ?? '';
                $value = $item['Value'] ?? '';
                $mpesaData[$name] = $value;
            }

            $receiptNumber = $mpesaData['MpesaReceiptNumber'] ?? null;
            $amount = $mpesaData['Amount'] ?? null;

            Log::info('Payment Success - Updating status', [
                'payment_id' => $payment->id,
                'receipt' => $receiptNumber,
                'amount' => $amount,
            ]);

            $payment->update([
                'mpesa_receipt' => $receiptNumber,
                'amount' => $amount ?? $payment->amount,
            ]);

            $this->paymentService->confirmPayment($payment, $mpesaData);

            $payment->refresh();

            Log::info('Payment completed successfully', [
                'payment_id' => $payment->id,
                'receipt_number' => $payment->receipt_number,
                'status' => $payment->status,
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Success',
            ]);
        } else {
            Log::warning('Payment Failed via callback', [
                'payment_id' => $payment->id,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
            ]);

            $this->paymentService->failPayment($payment, $resultDesc);

            return response()->json([
                'ResultCode' => $resultCode,
                'ResultDesc' => $resultDesc,
            ]);
        }
    }

    public function mpesaValidation(Request $request)
    {
        Log::info('M-PESA Validation Received', $request->all());
        
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function mpesaConfirmation(Request $request)
    {
        Log::info('M-PESA Confirmation Received', $request->all());
        
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function mpesaResult(Request $request)
    {
        Log::info('M-PESA Result Received', $request->all());
        
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function initiateFromForm(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^254[0-9]{9}$/',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        
        $application = Application::where('user_id', $user->id)
            ->whereIn('status', ['draft', 'pending'])
            ->latest()
            ->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'No active application found.']);
        }

        $existingPending = $application->payments()->where('status', 'pending')->first();
        if ($existingPending) {
            return response()->json(['success' => false, 'message' => 'A payment is already pending.']);
        }

        $result = $this->paymentService->initiateMpesa($application, $request->phone, 'application_fee');

        return response()->json($result);
    }

    public function submitBankFromForm(Request $request)
    {
        $user = auth()->user();
        
        $application = Application::where('user_id', $user->id)
            ->whereIn('status', ['draft', 'pending'])
            ->latest()
            ->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'No active application found.']);
        }

        $reference = $request->input('reference');
        
        if (!$reference) {
            return response()->json(['success' => false, 'message' => 'Transaction reference is required.']);
        }

        // Create pending payment record
        $amount = school_setting('application_fee', system_setting('application_fee', 2000));
        
        $payment = $application->payments()->create([
            'user_id' => $user->id,
            'amount' => $amount,
            'reference' => $reference,
            'method' => 'bank',
            'status' => 'pending',
            'paid_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Bank payment submitted. You will receive confirmation once verified.']);
    }
}
