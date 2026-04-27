<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class PaymentSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Global Payment Settings (minimum 1 KES)
            'admission_fee_amount' => ['value' => '2000', 'group' => 'payment'],
            'admission_fee_label' => ['value' => 'Application/Admission Fee', 'group' => 'payment'],
            'admission_fee_description' => ['value' => 'Non-refundable admission processing fee', 'group' => 'payment'],
            'commitment_fee_amount' => ['value' => '5000', 'group' => 'payment'],
            'commitment_fee_label' => ['value' => 'Commitment Fee', 'group' => 'payment'],
            'commitment_fee_description' => ['value' => 'Fee to secure your admission offer', 'group' => 'payment'],
            'commitment_fee_due_days' => ['value' => '14', 'group' => 'payment'],
            'currency' => ['value' => 'KES', 'group' => 'general'],
            'currency_symbol' => ['value' => 'KSh', 'group' => 'general'],
            'mpesa_paybill_number' => ['value' => '174379', 'group' => 'payment'],
            'mpesa_account_reference' => ['value' => 'ADM-{application_number}', 'group' => 'payment'],
            'payment_receipt_required' => ['value' => '0', 'group' => 'payment'],
            'mpesa_stk_enabled' => ['value' => '1', 'group' => 'payment'],
            'paypal_enabled' => ['value' => '0', 'group' => 'payment'],
            'manual_payment_enabled' => ['value' => '1', 'group' => 'payment'],
            // M-PESA Credentials
            'mpesa_shortcode' => ['value' => '174379', 'group' => 'payment'],
            'mpesa_passkey' => ['value' => '', 'group' => 'payment'],
            'mpesa_consumer_key' => ['value' => '', 'group' => 'payment'],
            'mpesa_consumer_secret' => ['value' => '', 'group' => 'payment'],
            // PayPal
            'paypal_mode' => ['value' => 'sandbox', 'group' => 'payment'],
            'paypal_client_id' => ['value' => '', 'group' => 'payment'],
            'paypal_secret' => ['value' => '', 'group' => 'payment'],
            // Stripe
            'stripe_public_key' => ['value' => '', 'group' => 'payment'],
            'stripe_secret_key' => ['value' => '', 'group' => 'payment'],
        ];

        foreach ($settings as $key => $data) {
            $existing = Setting::withoutGlobalScopes()
                ->where('key', $key)
                ->whereNull('school_id')
                ->first();
            
            if (!$existing) {
                Setting::withoutGlobalScopes()->create([
                    'key' => $key,
                    'school_id' => null,
                    'value' => $data['value'],
                    'group' => $data['group'],
                ]);
            } else {
                $existing->update([
                    'value' => $data['value'],
                    'group' => $data['group'],
                ]);
            }
        }

        $this->command->info('Payment settings seeded successfully!');
    }
}