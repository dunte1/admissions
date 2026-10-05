<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentLog;
use App\Models\Application;
use App\Models\School;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class PaymentService
{
    protected $mpesaConfig;
    protected $paypalConfig;
    protected const LOCK_TIMEOUT = 30;
    protected const DUPLICATE_WINDOW = 3600;

    public function __construct()
    {
        $this->mpesaConfig = config('services.mpesa');
        $this->paypalConfig = config('services.paypal');
    }

    public function initiateMpesa(Application $application, string $phone, string $paymentType = 'admission_fee'): array
    {
        $lockKey = "payment_lock_{$application->id}_{$phone}_{$paymentType}";
        $cacheLock = Cache::lock($lockKey, self::LOCK_TIMEOUT);

        if (!$cacheLock->get()) {
            return ['success' => false, 'message' => 'Payment is being processed. Please wait.'];
        }

        try {
            $amount = $this->getFeeAmount($application, $paymentType);
            $idempotencyKey = Payment::generateIdempotencyKey($application->id, $phone, 'mpesa');

            if ($this->isDuplicatePayment($idempotencyKey, $application->id)) {
                Log::warning('Duplicate payment attempt blocked', [
                    'application_id' => $application->id,
                    'idempotency_key' => $idempotencyKey,
                ]);
                return ['success' => false, 'message' => 'A similar payment was recently initiated. Please check your phone or try again later.'];
            }

            $existingPending = Payment::where('application_id', $application->id)
                ->where('payment_method', 'mpesa')
                ->where('payment_type', $paymentType)
                ->where('phone_number', $phone)
                ->where('status', 'pending')
                ->where('initiated_at', '>=', now()->subMinutes(30))
                ->first();

            if ($existingPending) {
                return ['success' => false, 'message' => 'You already have a pending M-PESA payment. Please check your phone or wait for it to timeout.'];
            }

            $token = $this->getMpesaToken();
            $reference = $this->getAccountReference($application);
            $callbackUrl = config('services.mpesa.callback_url') ?: route('mpesa.callback');
            
            $schoolName = $application->school ? $application->school->name : system_setting('system_name', 'School');
            $feeLabel = $paymentType === 'commitment_fee' ? 'Commitment Fee' : 'Admission Fee';
            
            $response = Http::withToken($token)->post(
                $this->mpesaConfig['stk_push_url'],
                [
                    'BusinessShortCode' => $this->mpesaConfig['shortcode'],
                    'Password' => $this->generateMpesaPassword(),
                    'Timestamp' => now()->format('YmdHis'),
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => $amount,
                    'PartyA' => $phone,
                    'PartyB' => $this->mpesaConfig['shortcode'],
                    'PhoneNumber' => $phone,
                    'CallBackURL' => $callbackUrl,
                    'AccountReference' => $reference,
                    'TransactionDesc' => "{$feeLabel} - {$schoolName} - {$reference}",
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('M-PESA STK Push Response', [
                    'application_id' => $application->id,
                    'response' => $data,
                ]);
                
                if (isset($data['CheckoutRequestID'])) {
                    $payment = Payment::create([
                        'school_id' => $application->school_id,
                        'application_id' => $application->id,
                        'user_id' => $application->user_id,
                        'amount' => $amount,
                        'payment_type' => $paymentType,
                        'payment_method' => 'mpesa',
                        'transaction_id' => $data['CheckoutRequestID'],
                        'phone_number' => $phone,
                        'status' => 'pending',
                        'idempotency_key' => $idempotencyKey,
                        'initiated_at' => now(),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);

                    PaymentLog::logInitiated($payment);
                    
                    AuditLog::log('payment_initiated', $payment, null, [
                        'amount' => $amount,
                        'payment_type' => $paymentType,
                        'method' => 'mpesa',
                        'phone' => substr($phone, 0, 4) . '****',
                    ]);

                    return ['success' => true, 'message' => 'Payment request sent to your phone', 'payment_id' => $payment->id];
                }
                
                // Check for M-PESA error codes
                $errorCode = $data['errorCode'] ?? $data['ResponseCode'] ?? null;
                $errorMessage = $data['errorMessage'] ?? $data['ResponseDescription'] ?? 'Unknown error';
                
                Log::warning('M-PESA STK Push returned no CheckoutRequestID', [
                    'application_id' => $application->id,
                    'response' => $data,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                ]);
                
                return ['success' => false, 'message' => "M-PESA Error: {$errorMessage}"];
            }

            Log::error('M-PESA STK Push Failed - Non-success response', [
                'application_id' => $application->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            return ['success' => false, 'message' => 'Failed to initiate M-PESA payment. Server returned: ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('M-PESA Error: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'payment_type' => $paymentType,
                'trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => 'Payment service unavailable: ' . $e->getMessage()];
        } finally {
            $cacheLock->release();
        }
    }

    protected function getFeeAmount(Application $application, string $paymentType): int
    {
        $schoolId = $application->school_id;
        
        if ($paymentType === 'commitment_fee') {
            $override = $this->getSchoolSetting($schoolId, 'commitment_fee_amount_override');
            if ($override !== null) {
                return (int) $override;
            }
            return (int) $this->getGlobalSetting('commitment_fee_amount', 5000);
        }
        
        $override = $this->getSchoolSetting($schoolId, 'admission_fee_amount_override');
        if ($override !== null) {
            return (int) $override;
        }
        
        return (int) $this->getGlobalSetting('admission_fee_amount', 2000);
    }

    protected function getGlobalSetting(string $key, $default = null)
    {
        return Setting::getGlobal($key, $default);
    }

    protected function getSchoolSetting(?int $schoolId, string $key)
    {
        if (!$schoolId) {
            return null;
        }
        
        $school = School::find($schoolId);
        return $school?->getConfig($key);
    }

    protected function getAccountReference(Application $application): string
    {
        $template = $this->getGlobalSetting('mpesa_account_reference', 'ADM-{application_number}');
        return str_replace('{application_number}', $application->application_number, $template);
    }

    public function getPaymentConfig(Application $application): array
    {
        $schoolId = $application->school_id;
        
        return [
            'admission_fee' => [
                'amount' => $this->getFeeAmount($application, 'admission_fee'),
                'label' => $this->getSchoolSetting($schoolId, 'admission_fee_label') 
                    ?? $this->getGlobalSetting('admission_fee_label', 'Application/Admission Fee'),
                'description' => $this->getSchoolSetting($schoolId, 'admission_fee_description')
                    ?? $this->getGlobalSetting('admission_fee_description', 'Non-refundable admission processing fee'),
                'currency' => $this->getGlobalSetting('currency', 'KES'),
                'currency_symbol' => $this->getGlobalSetting('currency_symbol', 'KSh'),
            ],
            'commitment_fee' => [
                'amount' => $this->getFeeAmount($application, 'commitment_fee'),
                'label' => $this->getSchoolSetting($schoolId, 'commitment_fee_label')
                    ?? $this->getGlobalSetting('commitment_fee_label', 'Commitment Fee'),
                'description' => $this->getSchoolSetting($schoolId, 'commitment_fee_description')
                    ?? $this->getGlobalSetting('commitment_fee_description', 'Fee to secure your admission offer'),
                'due_days' => (int) ($this->getGlobalSetting('commitment_fee_due_days', 14)),
                'currency' => $this->getGlobalSetting('currency', 'KES'),
                'currency_symbol' => $this->getGlobalSetting('currency_symbol', 'KSh'),
            ],
            'mpesa' => [
                'paybill' => $this->getSchoolSetting($schoolId, 'mpesa_shortcode')
                    ?? $this->getGlobalSetting('mpesa_paybill_number', ''),
                'stk_enabled' => (bool) ($this->getSchoolSetting($schoolId, 'payment_mpesa_enabled') 
                    ?? $this->getGlobalSetting('mpesa_stk_enabled', true)),
                'manual_enabled' => (bool) ($this->getSchoolSetting($schoolId, 'manual_payment_enabled')
                    ?? $this->getGlobalSetting('manual_payment_enabled', false)),
            ],
            'paypal_enabled' => (bool) ($this->getSchoolSetting($schoolId, 'payment_paypal_enabled')
                ?? $this->getGlobalSetting('paypal_enabled', false)),
            'payment_instructions' => $this->getSchoolSetting($schoolId, 'payment_instructions') ?? '',
        ];
    }

    public function initiatePayPal(Application $application): array
    {
        $idempotencyKey = Payment::generateIdempotencyKey($application->id, $application->user_id, 'paypal');

        if ($this->isDuplicatePayment($idempotencyKey, $application->id)) {
            return ['success' => false, 'message' => 'A similar payment was recently initiated.'];
        }

        try {
            $amount = $this->getApplicationFee($application->school_id);
            $currency = config('app-config.currency', 'USD');

            $payment = Payment::create([
                'school_id' => $application->school_id,
                'application_id' => $application->id,
                'user_id' => $application->user_id,
                'amount' => $amount,
                'payment_method' => 'paypal',
                'transaction_id' => 'PP-' . uniqid(),
                'status' => 'pending',
                'idempotency_key' => $idempotencyKey,
                'initiated_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            PaymentLog::logInitiated($payment);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'approval_url' => $this->getPayPalApprovalUrl($payment, $amount, $currency),
            ];
        } catch (\Exception $e) {
            Log::error('PayPal Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'PayPal service unavailable'];
        }
    }

    protected function isDuplicatePayment(string $idempotencyKey, int $applicationId): bool
    {
        return Cache::has("idempotency_{$idempotencyKey}") ||
               Payment::where('idempotency_key', $idempotencyKey)
                   ->where('created_at', '>=', now()->subSeconds(self::DUPLICATE_WINDOW))
                   ->exists();
    }

    public function verifyMpesaPayment(string $checkoutRequestId): array
    {
        try {
            $payment = Payment::where('transaction_id', $checkoutRequestId)->first();
            
            if ($payment) {
                PaymentLog::logVerified($payment);
            }

            $token = $this->getMpesaToken();

            $response = Http::withToken($token)->post(
                $this->mpesaConfig['stk_query_url'],
                [
                    'BusinessShortCode' => $this->mpesaConfig['shortcode'],
                    'Password' => $this->generateMpesaPassword(),
                    'Timestamp' => now()->format('YmdHis'),
                    'CheckoutRequestID' => $checkoutRequestId,
                ]
            );

            if ($response->successful()) {
                return $response->json();
            }

            return ['ResultCode' => -1, 'ResultDesc' => 'Query failed'];
        } catch (\Exception $e) {
            Log::error('M-PESA Query Error: ' . $e->getMessage());
            return ['ResultCode' => -1, 'ResultDesc' => 'Service unavailable'];
        }
    }

    protected function getMpesaToken(): string
    {
        $cacheKey = 'mpesa_token';
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::withBasicAuth(
            $this->mpesaConfig['consumer_key'],
            $this->mpesaConfig['consumer_secret']
        )->get($this->mpesaConfig['oauth_url']);

        $token = $response->json()['access_token'] ?? '';
        
        if ($token) {
            Cache::put($cacheKey, $token, 3300);
        }

        return $token;
    }

    protected function generateMpesaPassword(): string
    {
        $shortcode = $this->mpesaConfig['shortcode'];
        $passkey = $this->mpesaConfig['passkey'];
        $timestamp = now()->format('YmdHis');
        
        return base64_encode($shortcode . $passkey . $timestamp);
    }

    protected function getPayPalApprovalUrl(Payment $payment, $amount, $currency): string
    {
        Log::info('PayPal payment initiated (sandbox stub)', [
            'payment_id' => $payment->id,
            'amount' => $amount,
            'currency' => $currency,
        ]);

        // TODO: Implement actual PayPal API integration
        // The PayPal API requires a server-side PayPal SDK or direct REST API calls
        // See: https://developer.paypal.com/docs/checkout/standard/
        return config('services.paypal.mode') === 'live'
            ? 'https://www.paypal.com/checkoutnow?token=' . $payment->transaction_id
            : 'https://www.sandbox.paypal.com/checkoutnow?token=' . $payment->transaction_id;
    }

    public function confirmPayment(Payment $payment, array $mpesaData = []): bool
    {
        $receipt = $mpesaData['MpesaReceiptNumber'] ?? null;
        
        if ($payment->status === 'completed') {
            PaymentLog::logDuplicateAttempt($payment, $payment->mpesa_receipt ?? 'unknown');
            return true;
        }

        DB::transaction(function () use ($payment, $receipt) {
            $payment->markAsCompleted($receipt);
            
            PaymentLog::logCompleted($payment, $receipt);
            
            AuditLog::log('payment_completed', $payment, null, [
                'amount' => $payment->amount,
                'receipt' => $receipt,
            ]);
        });

        $this->triggerPaymentNotifications($payment);

        return true;
    }

    protected function triggerPaymentNotifications(Payment $payment): void
    {
        try {
            $notificationService = app(PaymentNotificationService::class);
            $notificationService->sendAllNotifications($payment);
        } catch (\Exception $e) {
            Log::error('Failed to send payment notifications', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failPayment(Payment $payment, ?string $reason = null): bool
    {
        $payment->markAsFailed($reason ?? 'Payment failed');
        PaymentLog::logFailed($payment, $reason ?? 'Unknown reason');
        
        AuditLog::log('payment_failed', $payment, null, [
            'reason' => $reason,
        ]);

        return true;
    }

    protected function getApplicationFee(?int $schoolId = null): int
    {
        $schoolId = $schoolId ?? School::getCurrentId();
        
        if ($schoolId) {
            $school = School::find($schoolId);
            $fee = $school?->getConfig('application_fee');
            if ($fee !== null) {
                return (int) $fee;
            }
        }
        
        return (int) config('app-config.application_fee', 2000);
    }

    public function getSchoolFee(?int $schoolId = null): array
    {
        $schoolId = $schoolId ?? School::getCurrentId();
        
        if ($schoolId) {
            $school = School::find($schoolId);
            return [
                'fee' => (int) ($school?->getConfig('application_fee') ?? config('app-config.application_fee', 2000)),
                'currency' => $school?->currency ?? 'KES',
                'currency_symbol' => $school?->currency_symbol ?? 'KSh',
            ];
        }
        
        return [
            'fee' => (int) config('app-config.application_fee', 2000),
            'currency' => config('app-config.currency', 'KES'),
            'currency_symbol' => config('app-config.currency_symbol', 'KSh'),
        ];
    }

    public function processCallback(array $callbackData): array
    {
        $result = $callbackData['Body']['stkCallback'] ?? null;

        if (!$result) {
            Log::error('Invalid M-PESA callback received', ['data' => $callbackData]);
            return ['success' => false, 'message' => 'Invalid callback data'];
        }

        $checkoutRequestId = $result['CheckoutRequestID'] ?? '';
        $resultCode = $result['ResultCode'] ?? -1;

        $payment = Payment::where('transaction_id', $checkoutRequestId)->first();

        if (!$payment) {
            Log::error('Payment not found for checkout ID', ['checkout_id' => $checkoutRequestId]);
            return ['success' => false, 'message' => 'Payment not found'];
        }

        $payment->update(['callback_received_at' => now()]);
        PaymentLog::logCallback($payment, $callbackData);

        if ($resultCode === 0) {
            $items = $result['CallbackMetadata']['Item'] ?? [];
            $mpesaData = [];

            foreach ($items as $item) {
                $name = $item['Name'] ?? '';
                $value = $item['Value'] ?? '';
                $mpesaData[$name] = $value;
            }

            $this->confirmPayment($payment, $mpesaData);

            return [
                'success' => true,
                'message' => 'Payment successful',
                'receipt' => $mpesaData['MpesaReceiptNumber'] ?? null,
            ];
        } else {
            $resultDesc = $result['ResultDesc'] ?? 'Payment failed';
            $this->failPayment($payment, $resultDesc);

            return [
                'success' => false,
                'message' => $resultDesc,
            ];
        }
    }

    public function timeoutPayment(Payment $payment): bool
    {
        if ($payment->status !== 'pending') {
            return false;
        }

        $timeoutThreshold = now()->subMinutes(30);
        
        if ($payment->initiated_at && $payment->initiated_at < $timeoutThreshold) {
            $this->failPayment($payment, 'Payment request timed out');
            PaymentLog::logTimeout($payment);
            return true;
        }

        return false;
    }

    public function createManualPayment(Application $application, string $transactionCode, string $phone, ?string $amount = null): array
    {
        try {
            $paymentType = $this->determinePaymentType($application);
            $feeAmount = $this->getFeeAmount($application, $paymentType);
            $actualAmount = $amount ?? $feeAmount;
            
            if ($paymentType === 'admission_fee') {
                $existingAdmissionPayment = $application->payments()
                    ->where('payment_type', 'admission_fee')
                    ->whereIn('status', ['completed', 'manual_pending'])
                    ->first();
                    
                if ($existingAdmissionPayment) {
                    return ['success' => false, 'message' => 'Admission fee has already been paid'];
                }
            }
            
            if ($paymentType === 'commitment_fee') {
                $existingCommitmentPayment = $application->payments()
                    ->where('payment_type', 'commitment_fee')
                    ->whereIn('status', ['completed', 'manual_pending'])
                    ->first();
                    
                if ($existingCommitmentPayment) {
                    return ['success' => false, 'message' => 'Commitment fee has already been paid'];
                }
            }

            $payment = Payment::create([
                'school_id' => $application->school_id,
                'application_id' => $application->id,
                'user_id' => $application->user_id,
                'amount' => $actualAmount,
                'payment_type' => $paymentType,
                'payment_method' => 'manual_mpesa',
                'transaction_id' => $transactionCode,
                'phone_number' => $phone,
                'status' => 'manual_pending',
                'initiated_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            PaymentLog::log($payment, 'manual_submitted', 'Manual payment submitted, pending verification');

            AuditLog::log('manual_payment_submitted', $payment, null, [
                'amount' => $actualAmount,
                'payment_type' => $paymentType,
                'transaction_code' => substr($transactionCode, 0, 4) . '****',
            ]);

            try {
                $notificationService = app(PaymentNotificationService::class);
                $notificationService->sendManualPaymentNotification($payment);
            } catch (\Exception $e) {
                Log::warning('Failed to send admin notification for manual payment', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return [
                'success' => true,
                'message' => 'Payment submitted successfully. It will be verified by an administrator within 24 hours.',
                'payment_id' => $payment->id,
            ];
        } catch (\Exception $e) {
            Log::error('Manual payment creation failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'Failed to submit manual payment'];
        }
    }

    protected function determinePaymentType(Application $application): string
    {
        $hasAdmissionPayment = $application->payments()
            ->where('payment_type', 'admission_fee')
            ->whereIn('status', ['completed', 'manual_pending'])
            ->exists();

        if (!$hasAdmissionPayment) {
            return 'admission_fee';
        }

        $hasCommitmentPayment = $application->payments()
            ->where('payment_type', 'commitment_fee')
            ->whereIn('status', ['completed', 'manual_pending'])
            ->exists();

        if (!$hasCommitmentPayment && in_array($application->status, ['offered', 'accepted'])) {
            return 'commitment_fee';
        }

        return 'admission_fee';
    }

    public function verifyManualPayment(Payment $payment): array
    {
        if ($payment->status !== 'manual_pending') {
            return ['success' => false, 'message' => 'Payment is not pending verification'];
        }

        try {
            $receipt = $payment->generateReceiptNumber();
            
            DB::transaction(function () use ($payment, $receipt) {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'completed_at' => now(),
                ]);
                
                PaymentLog::logCompleted($payment, $receipt);
                
                AuditLog::log('manual_payment_verified', $payment, auth()->id(), [
                    'receipt' => $receipt,
                    'verified_by' => auth()->id(),
                ]);
            });

            $this->triggerPaymentNotifications($payment);

            return [
                'success' => true,
                'message' => 'Payment verified successfully',
                'receipt' => $payment->receipt_number,
            ];
        } catch (\Exception $e) {
            Log::error('Manual payment verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'Failed to verify payment'];
        }
    }
}
