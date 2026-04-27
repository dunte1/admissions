<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingsService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GlobalSettingsController extends Controller
{
    protected SettingsService $settings;
    protected FileUploadService $fileUpload;

    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
        $this->settings = new SettingsService(null);
        $this->fileUpload = new FileUploadService();
    }

    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'system');
        
        $settings = $this->settings->all();
        $defaults = SettingsService::getDefaults();

        return view('super-admin.settings.index', compact(
            'activeTab',
            'settings',
            'defaults'
        ));
    }

    public function update(Request $request, string $group)
    {
        $rules = $this->getValidationRules($group);
        
        $fileFields = ['system_logo', 'favicon'];
        $nonFileRules = array_filter($rules, fn($key) => !in_array($key, $fileFields), ARRAY_FILTER_USE_KEY);
        
        if (empty($nonFileRules)) {
            $validated = [];
        } else {
            $validated = $request->validate($nonFileRules);
        }

        if ($group === 'branding') {
            $this->handleBrandingUploads($request);
        }

        if ($group === 'seo') {
            $this->handleSeoUploads($request);
        }

        if (!empty($validated)) {
            $this->settings->setMany($validated, $group);
        }
        
        Cache::forget('settings_cache');
        Cache::forget('settings_cache_global');

        return redirect()->back()->with('success', ucfirst($group) . ' settings updated successfully.');
    }

    protected function handleBrandingUploads(Request $request): void
    {
        $request->validate([
            'system_logo' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:png,ico,jpg,jpeg|max:512',
        ]);

        if ($request->hasFile('system_logo')) {
            $oldPath = system_setting('system_logo');
            $path = $this->fileUpload->uploadFile($request->file('system_logo'), 'system', $oldPath, null);
            if ($path) {
                $this->settings->set('system_logo', $path, 'branding');
            }
        }
        
        if ($request->hasFile('favicon')) {
            $oldPath = system_setting('favicon');
            $path = $this->fileUpload->uploadFile($request->file('favicon'), 'system', $oldPath, null);
            if ($path) {
                $this->settings->set('favicon', $path, 'branding');
            }
        }
    }

    protected function handleSeoUploads(Request $request): void
    {
        if ($request->hasFile('og_image')) {
            $oldPath = system_setting('og_image');
            $path = $this->fileUpload->uploadFile($request->file('og_image'), 'seo', $oldPath, null);
            if ($path) {
                $this->settings->set('og_image', $path, 'seo');
            }
        }
    }

    protected function getValidationRules(string $group): array
    {
        return match($group) {
            'system' => [
                'app_name' => 'required|string|max:255',
                'app_url' => 'nullable|url',
                'app_env' => 'nullable|in:local,development,production',
                'app_debug' => 'boolean',
                'maintenance_mode' => 'boolean',
            ],
            'general' => [
                'app_name' => 'required|string|max:255',
                'app_url' => 'nullable|url',
                'contact_email' => 'nullable|email',
                'contact_phone' => 'nullable|string|max:30',
                'timezone' => 'nullable|string|max:50',
                'locale' => 'nullable|in:en,sw',
                'currency' => 'nullable|string|max:10',
                'currency_symbol' => 'nullable|string|max:5',
            ],
            'mail' => [
                'mail_mailer' => 'nullable|in:smtp,mail,sendmail,log',
                'mail_host' => 'nullable|string',
                'mail_port' => 'nullable|integer',
                'mail_username' => 'nullable|string',
                'mail_password' => 'nullable|string',
                'mail_from_address' => 'nullable|email',
                'mail_from_name' => 'nullable|string',
                'mail_encryption' => 'nullable|in:tls,ssl,null',
            ],
            'payment' => [
                'payment_mpesa_enabled' => 'boolean',
                'payment_paypal_enabled' => 'boolean',
                'payment_card_enabled' => 'boolean',
                'mpesa_shortcode' => 'nullable|string|max:20',
                'mpesa_passkey' => 'nullable|string',
                'mpesa_consumer_key' => 'nullable|string',
                'mpesa_consumer_secret' => 'nullable|string',
                'paypal_client_id' => 'nullable|string',
                'paypal_secret' => 'nullable|string',
                'paypal_mode' => 'nullable|in:sandbox,live',
                'stripe_public_key' => 'nullable|string',
                'stripe_secret_key' => 'nullable|string',
                'admission_fee_amount' => 'nullable|integer|min:1|max:100000',
                'admission_fee_label' => 'nullable|string|max:255',
                'admission_fee_description' => 'nullable|string',
                'commitment_fee_amount' => 'nullable|integer|min:1|max:100000',
                'commitment_fee_label' => 'nullable|string|max:255',
                'commitment_fee_description' => 'nullable|string',
                'commitment_fee_due_days' => 'nullable|integer|min:1|max:60',
                'currency' => 'nullable|string|max:10',
                'currency_symbol' => 'nullable|string|max:5',
                'mpesa_paybill_number' => 'nullable|string|max:20',
                'mpesa_account_reference' => 'nullable|string|max:100',
                'payment_receipt_required' => 'boolean',
                'mpesa_stk_enabled' => 'boolean',
                'paypal_enabled' => 'boolean',
                'manual_payment_enabled' => 'boolean',
            ],
            'sms' => [
                'sms_api_key' => 'nullable|string',
                'sms_sender_id' => 'nullable|string',
                'sms_notifications_enabled' => 'boolean',
            ],
            'security' => [
                'password_min_length' => 'nullable|integer|min:8|max:32',
                'password_require_uppercase' => 'boolean',
                'password_require_lowercase' => 'boolean',
                'password_require_numbers' => 'boolean',
                'password_require_special' => 'boolean',
                'session_timeout' => 'nullable|integer|min:15|max:480',
                'max_login_attempts' => 'nullable|integer|min:3|max:10',
                'lockout_duration' => 'nullable|integer|min:1|max:60',
                'enable_2fa' => 'boolean',
            ],
            'features' => [
                'enable_admissions' => 'boolean',
                'enable_payments' => 'boolean',
                'enable_notifications' => 'boolean',
                'enable_multi_school' => 'boolean',
                'enable_student_registration' => 'boolean',
                'enable_document_upload' => 'boolean',
                'enable_sms_reminders' => 'boolean',
            ],
            'branding' => [
                'app_name' => 'nullable|string|max:255',
                'primary_color' => 'nullable|string|max:20',
                'secondary_color' => 'nullable|string|max:20',
                'accent_color' => 'nullable|string|max:20',
                'footer_text' => 'nullable|string|max:255',
                'show_footer_branding' => 'nullable|boolean',
            ],
            'seo' => [
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:255',
                'og_title' => 'nullable|string|max:255',
                'og_description' => 'nullable|string|max:500',
                'og_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'twitter_card' => 'nullable|in:summary,summary_large_image',
                'canonical_url' => 'nullable|url',
            ],
            default => [],
        };
    }

    public function clearCache()
    {
        Cache::flush();
        
        return redirect()->back()->with('success', 'Application cache cleared successfully.');
    }
}