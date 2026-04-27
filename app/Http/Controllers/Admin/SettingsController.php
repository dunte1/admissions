<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Setting;
use App\Services\SettingsService;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    protected SettingsService $settings;
    protected FileUploadService $fileUpload;
    protected array $availableGroups;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->settings = new SettingsService();
        $this->fileUpload = new FileUploadService();
        $this->availableGroups = SettingsService::getSettingGroups();
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $allowedGroups = [];
        foreach ($this->availableGroups as $key => $group) {
            if ($user->hasAnyRole($group['roles'])) {
                $allowedGroups[$key] = $group;
            }
        }

        $activeTab = $request->query('tab', 'general');
        
        if (!isset($allowedGroups[$activeTab])) {
            $activeTab = array_key_first($allowedGroups);
        }

        $school = current_school();
        
        $settings = $this->settings->all();
        $defaults = SettingsService::getDefaults();

        return view('admin.settings.index', compact(
            'allowedGroups',
            'activeTab',
            'settings',
            'defaults',
            'school'
        ));
    }

    public function update(Request $request, string $group)
    {
        if (!$this->settings->canAccessGroup($group)) {
            abort(403, 'You do not have permission to access this settings group.');
        }

        $rules = $this->getValidationRules($group);
        $validated = $request->validate($rules);

        $settingsToSave = [];
        foreach ($validated as $key => $value) {
            if ($request->has($key)) {
                $settingsToSave[$key] = $value;
            }
        }

        if ($group === 'branding') {
            $this->handleBrandingUploads($request);
            
            if ($request->has('enable_white_label')) {
                $school = current_school();
                if ($school) {
                    $school->update(['enable_white_label' => $request->boolean('enable_white_label')]);
                }
            }
        }

        $this->settings->setMany($settingsToSave, $group);
        
        Cache::forget('settings_cache');
        if (current_school_id()) {
            Cache::forget('settings_cache_' . current_school_id());
        }

        return redirect()->back()->with('success', ucfirst($group) . ' settings updated successfully.');
    }

    protected function handleBrandingUploads(Request $request): void
    {
        $school = current_school();
        
        if ($request->hasFile('logo')) {
            $oldPath = $school?->logo;
            $path = $this->fileUpload->uploadFile($request->file('logo'), 'branding', $oldPath);
            if ($path && $school) {
                $school->update(['logo' => $path]);
            }
        }
        
        if ($request->hasFile('favicon')) {
            $oldPath = $school?->favicon;
            $path = $this->fileUpload->uploadFile($request->file('favicon'), 'branding', $oldPath);
            if ($path && $school) {
                $school->update(['favicon' => $path]);
            }
        }
    }

    protected function getValidationRules(string $group): array
    {
        return match($group) {
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
            'admission' => [
                'admissions_open' => 'boolean',
                'application_fee' => 'nullable|numeric|min:0',
                'application_start_date' => 'nullable|date',
                'application_end_date' => 'nullable|date',
                'max_applications_per_student' => 'nullable|integer|min:1|max:10',
                'require_payment_before_submission' => 'boolean',
                'allow_multiple_programs' => 'boolean',
                'require_document_verification' => 'boolean',
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
                'admission_fee_amount_override' => 'nullable|integer|min:1|max:100000',
                'commitment_fee_amount_override' => 'nullable|integer|min:1|max:100000',
                'mpesa_account_reference' => 'nullable|string|max:100',
                'manual_payment_enabled' => 'boolean',
                'payment_instructions' => 'nullable|string',
            ],
            'email' => [
                'mail_mailer' => 'nullable|in:smtp,mail,sendmail',
                'mail_host' => 'nullable|string',
                'mail_port' => 'nullable|integer',
                'mail_username' => 'nullable|string',
                'mail_password' => 'nullable|string',
                'mail_from_address' => 'nullable|email',
                'mail_from_name' => 'nullable|string',
                'mail_encryption' => 'nullable|in:tls,ssl,null',
                'email_notifications_enabled' => 'boolean',
                'sms_notifications_enabled' => 'boolean',
                'sms_api_key' => 'nullable|string',
                'sms_sender_id' => 'nullable|string',
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
                'ip_whitelist' => 'nullable|string',
            ],
            'branding' => [
                'logo' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:2048',
                'favicon' => 'nullable|image|mimes:png,ico,jpg,jpeg|max:512',
                'primary_color' => 'nullable|string|max:20',
                'secondary_color' => 'nullable|string|max:20',
                'accent_color' => 'nullable|string|max:20',
                'footer_text' => 'nullable|string|max:255',
                'enable_white_label' => 'nullable|boolean',
            ],
            'document' => [
                'required_documents' => 'nullable|array',
                'document_max_size' => 'nullable|integer|min:1|max:50',
                'allowed_document_types' => 'nullable|array',
            ],
            default => [],
        };
    }
}