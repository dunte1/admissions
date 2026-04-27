<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Services\PaymentService;
use Illuminate\Console\Command;

class TestMpesaCredentials extends Command
{
    protected $signature = 'mpesa:test {phone=0746979588} {amount=1}';
    protected $description = 'Test M-PESA STK Push credentials';

    public function __construct(private PaymentService $paymentService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $phone = $this->argument('phone');
        $amount = (int) $this->argument('amount');

        if (!str_starts_with($phone, '254')) {
            $phone = '254' . substr($phone, 1);
        }

        $this->info("Testing M-PESA STK Push...");
        $this->info("Phone: {$phone}");
        $this->info("Amount: {$amount} KES");

        try {
            $mpesaConfig = config('services.mpesa');
            
            $oauthUrl = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
            
            $this->info('Using OAuth URL: ' . $oauthUrl);
            $this->info('Consumer Key: ' . substr($mpesaConfig['consumer_key'], 0, 10) . '...');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $oauthUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $mpesaConfig['consumer_key'] . ':' . $mpesaConfig['consumer_secret']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            
            $oauthResponse = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $this->info('OAuth HTTP Code: ' . $httpCode);
            $this->info('OAuth Response: ' . $oauthResponse);
            
            $tokenData = json_decode($oauthResponse, true);
            $token = $tokenData['access_token'] ?? '';
            
            if (empty($token)) {
                $this->error('Failed to get M-PESA token. Check your credentials.');
                return self::FAILURE;
            }

            $this->info('Token obtained successfully!');

            $application = Application::first();
            
            if (!$application) {
                $this->warn('No application found. Testing without application...');
                
                $stkPushUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
                
                $this->info('Using STK Push URL: ' . $stkPushUrl);
                $this->info('Token (first 20 chars): ' . substr($token, 0, 20));
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $stkPushUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                    'BusinessShortCode' => $mpesaConfig['shortcode'],
                    'Password' => $this->generatePassword(),
                    'Timestamp' => now()->format('YmdHis'),
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => $amount,
                    'PartyA' => $phone,
                    'PartyB' => $mpesaConfig['shortcode'],
                    'PhoneNumber' => $phone,
                    'CallBackURL' => $mpesaConfig['callback_url'] ?? 'https://mutomo1.duncowebsolutions.co.ke/mpesa/callback',
                    'AccountReference' => 'TEST',
                    'TransactionDesc' => 'Test Payment',
                ]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $token,
                    'Content-Type: application/json',
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                
                $stkResponse = curl_exec($ch);
                $stkHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                $this->info('STK HTTP Code: ' . $stkHttpCode);
                $this->info('STK Response: ' . $stkResponse);
                
                $data = json_decode($stkResponse, true);

                if (isset($data['CheckoutRequestID'])) {
                    $this->info('STK Push initiated successfully!');
                    $this->info('CheckoutRequestID: ' . $data['CheckoutRequestID']);
                    return self::SUCCESS;
                } else {
                    $this->error('Failed: ' . ($data['errorMessage'] ?? 'Unknown error'));
                    return self::FAILURE;
                }
            }

            $result = $this->paymentService->initiateMpesa($application, $phone);

            if ($result['success']) {
                $this->info('STK Push initiated successfully!');
                $this->info('Message: ' . $result['message']);
                return self::SUCCESS;
            } else {
                $this->error('Failed: ' . $result['message']);
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function generatePassword(): string
    {
        $shortcode = config('services.mpesa.shortcode');
        $passkey = config('services.mpesa.passkey');
        $timestamp = now()->format('YmdHis');
        return base64_encode($shortcode . $passkey . $timestamp);
    }
}