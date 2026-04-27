<form action="{{ route('admin.settings.update', 'payment') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Payment Settings</h2>
                <p class="text-sm text-gray-500">Configure payment gateways, fees, and options</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-8">
            {{-- Fee Overrides --}}
            <div>
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-edit text-green-600"></i>
                    </span>
                    Fee Overrides (Optional)
                </h3>
                <p class="text-sm text-gray-500 mb-4 ml-11">Override global fee settings for your school</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 ml-11">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Admission Fee Override (KES)</label>
                        <input type="number" name="admission_fee_amount_override" value="{{ old('admission_fee_amount_override', $settings['admission_fee_amount_override'] ?? '') }}"
                            placeholder="Leave empty to use global"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            min="0" step="100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Commitment Fee Override (KES)</label>
                        <input type="number" name="commitment_fee_amount_override" value="{{ old('commitment_fee_amount_override', $settings['commitment_fee_amount_override'] ?? '') }}"
                            placeholder="Leave empty to use global"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            min="0" step="100">
                    </div>
                </div>
            </div>

            {{-- Payment Methods --}}
            <div>
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-credit-card text-blue-600"></i>
                    </span>
                    Enabled Payment Methods
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 ml-11">
                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="payment_mpesa_enabled" value="0">
                        <input type="checkbox" name="payment_mpesa_enabled" value="1"
                            {{ old('payment_mpesa_enabled', $settings['payment_mpesa_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">M-PESA</span>
                            <span class="block text-xs text-gray-500">Safaricom Mobile Payment</span>
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="payment_paypal_enabled" value="0">
                        <input type="checkbox" name="payment_paypal_enabled" value="1"
                            {{ old('payment_paypal_enabled', $settings['payment_paypal_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">PayPal</span>
                            <span class="block text-xs text-gray-500">International Payments</span>
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="payment_card_enabled" value="0">
                        <input type="checkbox" name="payment_card_enabled" value="1"
                            {{ old('payment_card_enabled', $settings['payment_card_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Card Payment</span>
                            <span class="block text-xs text-gray-500">Visa/Mastercard via Stripe</span>
                        </span>
                    </label>
                </div>
            </div>

            {{-- M-PESA Configuration --}}
            <div>
                <h3 class="text-md font-semibold text-gray-900 mb-4">M-PESA Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Business Shortcode (Paybill/Till)</label>
                        <input type="text" name="mpesa_shortcode" 
                            value="{{ old('mpesa_shortcode', $settings['mpesa_shortcode'] ?? '') }}"
                            placeholder="174379"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passkey</label>
                        <input type="password" name="mpesa_passkey" 
                            value="{{ old('mpesa_passkey', $settings['mpesa_passkey'] ?? '') }}"
                            placeholder="••••••••••••"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consumer Key</label>
                        <input type="text" name="mpesa_consumer_key" 
                            value="{{ old('mpesa_consumer_key', $settings['mpesa_consumer_key'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consumer Secret</label>
                        <input type="password" name="mpesa_consumer_secret" 
                            value="{{ old('mpesa_consumer_secret', $settings['mpesa_consumer_secret'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Reference Format</label>
                        <input type="text" name="mpesa_account_reference" 
                            value="{{ old('mpesa_account_reference', $settings['mpesa_account_reference'] ?? 'ADM-{application_number}') }}"
                            placeholder="ADM-{application_number}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Use {application_number} as placeholder</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Manual Payment Enabled</label>
                        <select name="manual_payment_enabled" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="0" {{ ($settings['manual_payment_enabled'] ?? '0') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ ($settings['manual_payment_enabled'] ?? '0') == '1' ? 'selected' : '' }}>Yes - Students enter M-PESA code</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Payment Instructions --}}
            <div>
                <h3 class="text-md font-semibold text-gray-900 mb-4">Payment Instructions</h3>
                <div class="ml-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Instructions (shown to students)</label>
                    <textarea name="payment_instructions" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Enter any special payment instructions for students...">{{ old('payment_instructions', $settings['payment_instructions'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">PayPal Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                        <select name="paypal_mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="sandbox" {{ ($settings['paypal_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                            <option value="live" {{ ($settings['paypal_mode'] ?? 'sandbox') === 'live' ? 'selected' : '' }}>Live</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                        <input type="text" name="paypal_client_id" 
                            value="{{ old('paypal_client_id', $settings['paypal_client_id'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret</label>
                        <input type="password" name="paypal_secret" 
                            value="{{ old('paypal_secret', $settings['paypal_secret'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Stripe Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                        <input type="text" name="stripe_public_key" 
                            value="{{ old('stripe_public_key', $settings['stripe_public_key'] ?? '') }}"
                            placeholder="pk_test_..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                        <input type="password" name="stripe_secret_key" 
                            value="{{ old('stripe_secret_key', $settings['stripe_secret_key'] ?? '') }}"
                            placeholder="sk_test_..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>