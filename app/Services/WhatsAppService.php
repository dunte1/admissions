<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppService
{
    protected ?string $sid;
    protected ?string $token;
    protected ?string $whatsappNumber;
    protected bool $enabled;

    public function __construct()
    {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->whatsappNumber = config('services.twilio.phone_number');
        $this->enabled = config('services.twilio.enabled', false);
    }

    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->sid) && !empty($this->token) && !empty($this->whatsappNumber);
    }

    public function sendPaymentConfirmation(Payment $payment): array
    {
        if (!$this->isEnabled()) {
            Log::info('WhatsApp disabled, skipping payment confirmation', [
                'payment_id' => $payment->id,
            ]);
            return ['success' => false, 'message' => 'WhatsApp notifications disabled'];
        }

        try {
            $application = $payment->application()->with(['intake', 'user'])->first();
            $school = $payment->school;
            $user = $application->user;

            $phone = $this->formatPhoneForWhatsApp($user->phone);
            
            if (!$phone) {
                Log::warning('Invalid phone number for WhatsApp', [
                    'payment_id' => $payment->id,
                    'phone' => $user->phone,
                ]);
                return ['success' => false, 'message' => 'Invalid phone number'];
            }

            $variables = $this->prepareTemplateVariables($payment, $application, $school);

            $response = $this->sendTemplateMessage($phone, 'payment_confirmed', $variables);

            if ($response['success']) {
                PaymentLog::create([
                    'payment_id' => $payment->id,
                    'school_id' => $payment->school_id,
                    'user_id' => $payment->user_id,
                    'event' => 'whatsapp_sent',
                    'description' => 'WhatsApp payment confirmation sent',
                    'metadata' => [
                        'sid' => $response['sid'] ?? null,
                        'to' => $phone,
                        'template' => 'payment_confirmed',
                    ],
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('WhatsApp send error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'school_id' => $payment->school_id,
                'user_id' => $payment->user_id,
                'event' => 'whatsapp_failed',
                'description' => 'WhatsApp sending failed: ' . $e->getMessage(),
                'metadata' => ['error' => $e->getMessage()],
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    protected function formatPhoneForWhatsApp(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (Str::startsWith($phone, '0') && strlen($phone) === 10) {
            $phone = '254' . substr($phone, 1);
        }

        if (Str::startsWith($phone, '254') && strlen($phone) === 12) {
            return 'whatsapp:' . $phone;
        }

        if (Str::startsWith($phone, '7') && strlen($phone) === 9) {
            return 'whatsapp:254' . $phone;
        }

        return null;
    }

    protected function prepareTemplateVariables(Payment $payment, $application, $school): array
    {
        $studentName = trim(($application->first_name ?? '') . ' ' . ($application->last_name ?? ''));
        $amount = $payment->formatted_amount;
        $receiptNumber = $payment->receipt_number ?? 'N/A';
        $applicationNumber = $application->application_number ?? 'N/A';
        $schoolName = $school->name ?? 'Institution';
        $paymentMethod = ucfirst($payment->payment_method);
        $date = $payment->paid_at?->format('d M Y') ?? now()->format('d M Y');

        return [
            $studentName,
            $amount,
            $receiptNumber,
            $applicationNumber,
            $schoolName,
            $paymentMethod,
            $date,
            'Application Fee',
        ];
    }

    protected function sendTemplateMessage(string $to, string $templateName, array $variables): array
    {
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json";

        $body = [
            'From' => $this->whatsappNumber,
            'To' => $to,
            'ContentType' => 'application/json',
            'MessagingServiceSid' => null,
        ];

        $variableData = [];
        foreach ($variables as $index => $value) {
            $variableData[] = [
                'variable' => ($index + 1),
                'value' => $value,
            ];
        }

        $body['Body'] = json_encode([
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => 'en'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => array_map(fn($v) => ['type' => 'text', 'text' => $v], $variables),
                    ],
                ],
            ],
        ]);

        $response = $this->makeRequest('POST', $url, $body);

        if (isset($response['sid'])) {
            return [
                'success' => true,
                'sid' => $response['sid'],
                'message' => 'WhatsApp message sent successfully',
            ];
        }

        Log::warning('Twilio WhatsApp response missing SID', $response);

        return [
            'success' => true,
            'sid' => $response['sid'] ?? 'unknown',
            'message' => 'WhatsApp message sent (SID may not be available)',
        ];
    }

    protected function makeRequest(string $method, string $url, array $body = []): array
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->sid . ':' . $this->token);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $data ?? ['success' => true];
        }

        Log::error('Twilio API error', [
            'http_code' => $httpCode,
            'response' => $data,
        ]);

        throw new \Exception($data['message'] ?? 'Failed to send WhatsApp message');
    }
}