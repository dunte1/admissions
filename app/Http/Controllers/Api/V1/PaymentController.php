<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $payments = Payment::with('application')
            ->where('school_id', $request->user()->school_id)
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'transaction_id' => $payment->transaction_id,
                    'receipt_number' => $payment->receipt_number,
                    'application' => $payment->application ? [
                        'id' => $payment->application->id,
                        'application_number' => $payment->application->application_number,
                    ] : null,
                    'created_at' => $payment->created_at,
                ];
            }),
        ]);
    }

    public function show(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $payment->load('application.program');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
                'receipt_number' => $payment->receipt_number,
                'phone' => $payment->phone,
                'application' => $payment->application ? [
                    'id' => $payment->application->id,
                    'application_number' => $payment->application->application_number,
                    'program' => $payment->application->program->name ?? null,
                ] : null,
                'created_at' => $payment->created_at,
            ],
        ]);
    }

    public function initiate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'application_id' => 'required|exists:applications,id',
            'payment_method' => 'required|in:mpesa,paypal,bank',
            'phone' => 'required_if:payment_method,mpesa|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $application = Application::find($request->application_id);
        
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($application->school->application_fee <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No application fee required',
            ], 400);
        }

        $payment = Payment::create([
            'user_id' => $request->user()->id,
            'school_id' => $request->user()->school_id,
            'application_id' => $application->id,
            'amount' => $application->school->application_fee,
            'currency' => 'KES',
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'phone' => $request->phone,
        ]);

        $response = null;
        
        if ($request->payment_method === 'mpesa') {
            // Initiate M-PESA STK Push
            $response = app(\App\Services\PaymentService::class)->initiateMpesaPayment($payment, $request->phone);
        } elseif ($request->payment_method === 'paypal') {
            // Create PayPal payment
            $response = app(\App\Services\PaymentService::class)->initiatePaypalPayment($payment);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated',
            'data' => [
                'payment_id' => $payment->id,
                'checkout_url' => $response['checkout_url'] ?? null,
                'payment_method' => $payment->payment_method,
            ],
        ]);
    }

    public function status(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payment->id,
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'message' => $payment->status_message,
            ],
        ]);
    }

    public function callback(Request $request): JsonResponse
    {
        // M-PESA callback handler
        $validator = Validator::make($request->all(), [
            'TransID' => 'required|string',
            'TransAmount' => 'required|numeric',
            'MSISDN' => 'required|string',
            'ResultCode' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false], 400);
        }

        $payment = Payment::where('phone', $request->MSISDN)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        if ($request->ResultCode === 0) {
            $payment->update([
                'status' => 'completed',
                'transaction_id' => $request->TransID,
                'paid_at' => now(),
                'status_message' => 'Payment successful',
            ]);
        } else {
            $payment->update([
                'status' => 'failed',
                'status_message' => $request->ResultDesc ?? 'Payment failed',
            ]);
        }

        return response()->json(['success' => true]);
    }
}