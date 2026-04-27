<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $globalSettings = [
            'general' => [
                'app_name' => config('app.name', 'Admission Portal'),
                'app_url' => config('app.url', url('/')),
                'timezone' => 'Africa/Nairobi',
                'locale' => 'en',
                'currency' => 'KES',
                'currency_symbol' => 'KSh',
                'contact_email' => 'info@duncowebsolutions.com',
                'contact_phone' => '+254700000000',
            ],
            'admission' => [
                'admissions_open' => '1',
                'application_fee' => '2000',
                'application_start_date' => '',
                'application_end_date' => '',
                'max_applications_per_student' => '3',
                'require_payment_before_submission' => '1',
                'allow_multiple_programs' => '1',
                'require_document_verification' => '1',
            ],
            'payment' => [
                'payment_mpesa_enabled' => '0',
                'payment_paypal_enabled' => '0',
                'payment_card_enabled' => '0',
                'mpesa_shortcode' => '',
                'mpesa_passkey' => '',
                'mpesa_consumer_key' => '',
                'mpesa_consumer_secret' => '',
                'paypal_client_id' => '',
                'paypal_secret' => '',
                'paypal_mode' => 'sandbox',
            ],
            'email' => [
                'mail_mailer' => 'smtp',
                'mail_host' => '',
                'mail_port' => '587',
                'mail_username' => '',
                'mail_password' => '',
                'mail_from_address' => '',
                'mail_from_name' => config('app.name', 'Admission Portal'),
                'mail_encryption' => 'tls',
                'email_notifications_enabled' => '1',
                'sms_notifications_enabled' => '0',
                'sms_api_key' => '',
                'sms_sender_id' => '',
            ],
            'security' => [
                'password_min_length' => '8',
                'password_require_uppercase' => '1',
                'password_require_lowercase' => '1',
                'password_require_numbers' => '1',
                'password_require_special' => '0',
                'session_timeout' => '60',
                'max_login_attempts' => '5',
                'lockout_duration' => '15',
                'enable_2fa' => '0',
                'ip_whitelist' => '',
            ],
            'branding' => [
                'system_name' => config('app.name', 'Admission Portal'),
                'tagline' => 'Streamline Admissions for Schools, Colleges & Universities',
                'system_logo' => null,
                'favicon' => null,
                'primary_color' => '#00008B',
                'secondary_color' => '#10B981',
                'accent_color' => '#F59E0B',
                'footer_text' => '© ' . date('Y') . ' ' . config('app.name', 'Admission Portal') . '. Powered by Duncowebsolutions',
                'show_footer_branding' => '1',
            ],
            'document' => [
                'required_documents' => json_encode(['id_document', 'kcse_certificate', 'birth_certificate']),
                'document_max_size' => '5',
                'allowed_document_types' => json_encode(['pdf', 'jpg', 'png']),
            ],
        ];

        foreach ($globalSettings as $group => $settings) {
            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key, 'school_id' => null],
                    [
                        'value' => $value,
                        'group' => $group,
                    ]
                );
            }
        }
    }
}