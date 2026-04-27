@extends('layouts.super-admin')

@section('header', $school->name . ' Settings')
@section('breadcrumb', 'Configure school-specific settings')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $school->name }} Settings</h1>
            <p class="text-sm text-gray-500">Configure school-specific settings</p>
        </div>
        <a href="{{ route('super-admin.schools.show', $school) }}" class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('super-admin.schools.settings.update', $school) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Settings</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Application Fee</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                {{ $school->currency_symbol ?? 'KSh' }}
                            </span>
                            <input type="number" name="application_fee" value="{{ old('application_fee', $settings['application_fee'] ?? 2000) }}" step="1" min="1"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="allow_multiple_applications" id="allow_multiple" value="1"
                            {{ old('allow_multiple_applications', $settings['allow_multiple_applications'] ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="allow_multiple" class="ml-2 block text-sm text-gray-700">
                            Allow multiple applications per student
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="require_payment_before_submission" id="require_payment" value="1"
                            {{ old('require_payment_before_submission', $settings['require_payment_before_submission'] ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="require_payment" class="ml-2 block text-sm text-gray-700">
                            Require payment before submission
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Settings</h3>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Application Fee</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                {{ $school->currency_symbol ?? 'KSh' }}
                            </span>
                            <input type="number" name="application_fee" value="{{ old('application_fee', $settings['application_fee'] ?? 2000) }}"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-purple-500" min="1" step="1">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Admission Fee</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                {{ $school->currency_symbol ?? 'KSh' }}
                            </span>
                            <input type="number" name="admission_fee_amount" value="{{ old('admission_fee_amount', $settings['admission_fee_amount'] ?? 5000) }}"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-purple-500" min="1" step="1">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Commitment Fee</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                {{ $school->currency_symbol ?? 'KSh' }}
                            </span>
                            <input type="number" name="commitment_fee_amount" value="{{ old('commitment_fee_amount', $settings['commitment_fee_amount'] ?? 2000) }}"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-purple-500" min="1" step="1">
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Payment Methods</h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="hidden" name="payment_mpesa_enabled" value="0">
                                <input type="checkbox" name="payment_mpesa_enabled" value="1"
                                    {{ old('payment_mpesa_enabled', $settings['payment_mpesa_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">M-PESA</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="hidden" name="payment_manual_enabled" value="0">
                                <input type="checkbox" name="payment_manual_enabled" value="1"
                                    {{ old('payment_manual_enabled', $settings['payment_manual_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Cash / Manual Payment</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="hidden" name="payment_bank_enabled" value="0">
                                <input type="checkbox" name="payment_bank_enabled" value="1"
                                    {{ old('payment_bank_enabled', $settings['payment_bank_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Bank Transfer</span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4" x-data="{ mpesaEnabled: {{ old('payment_mpesa_enabled', $settings['payment_mpesa_enabled'] ?? '0') == '1' ? 'true' : 'false' }}">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">M-PESA Configuration</h4>
                        
                        <div class="space-y-3" x-show="mpesaEnabled">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Shortcode</label>
                                <input type="text" name="mpesa_shortcode" value="{{ old('mpesa_shortcode', $settings['mpesa_shortcode'] ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Passkey</label>
                                <input type="password" name="mpesa_passkey" value="{{ old('mpesa_passkey', $settings['mpesa_passkey'] ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4" x-data="{ bankEnabled: {{ old('payment_bank_enabled', $settings['payment_bank_enabled'] ?? '1') == '1' ? 'true' : 'false' }}">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Bank Details</h4>
                        
                        <div class="space-y-3" x-show="bankEnabled">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Bank Name</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name', $settings['bank_name'] ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Account Name</label>
                                <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $settings['bank_account_name'] ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Account Number</label>
                                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings['bank_account_number'] ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Branch</label>
                                <input type="text" name="bank_branch" value="{{ old('bank_branch', $settings['bank_branch'] ?? '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Email Settings</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                        <input type="email" name="email_from_address" value="{{ old('email_from_address', $settings['email_from_address'] ?? $school->email) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                        <input type="text" name="email_from_name" value="{{ old('email_from_name', $settings['email_from_name'] ?? $school->name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">SMS Settings</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="sms_enabled" id="sms_enabled" value="1"
                            {{ old('sms_enabled', $settings['sms_enabled'] ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="sms_enabled" class="ml-2 block text-sm text-gray-700">
                            Enable SMS notifications
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMS API Key</label>
                        <input type="password" name="sms_api_key" value="{{ old('sms_api_key', $settings['sms_api_key'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Admission Number Format</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prefix</label>
                        <input type="text" name="admission_prefix" value="{{ old('admission_prefix', $school->admission_prefix ?? 'MUT') }}"
                            placeholder="MUT"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <p class="mt-1 text-xs text-gray-500">e.g., MUT, KMTC, UNI</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Suffix (Optional)</label>
                        <input type="text" name="admission_suffix" value="{{ old('admission_suffix', $school->admission_suffix ?? '') }}"
                            placeholder=""
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="admission_include_year" id="include_year" value="1"
                            {{ old('admission_include_year', $school->admission_include_year ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="include_year" class="ml-2 block text-sm text-gray-700">
                            Include Year (e.g., 2026)
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="admission_include_program_code" id="include_program" value="1"
                            {{ old('admission_include_program_code', $school->admission_include_program_code ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="include_program" class="ml-2 block text-sm text-gray-700">
                            Include Program Code
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number Padding</label>
                        <select name="admission_number_padding" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="3" {{ ($school->admission_number_padding ?? 4) == 3 ? 'selected' : '' }}>3 digits (001)</option>
                            <option value="4" {{ ($school->admission_number_padding ?? 4) == 4 ? 'selected' : '' }}>4 digits (0001)</option>
                            <option value="5" {{ ($school->admission_number_padding ?? 4) == 5 ? 'selected' : '' }}>5 digits (00001)</option>
                            <option value="6" {{ ($school->admission_number_padding ?? 4) == 6 ? 'selected' : '' }}>6 digits (000001)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <strong>Preview Format:</strong> 
                        <code class="bg-gray-200 px-2 py-1 rounded text-xs">
                            {{ $school->admission_prefix ?? 'MUT' }}-{{ date('Y') }}-{{ $school->admission_include_program_code ?? true ? 'PROG-' : '' }}0001{{ $school->admission_suffix ? '-' . $school->admission_suffix : '' }}
                        </code>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-save mr-2"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
