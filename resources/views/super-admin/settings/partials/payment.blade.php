<form action="{{ route('super-admin.settings.update', 'payment') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Payment Configuration</h2>
                <p class="text-sm text-gray-500">Global payment gateway and fee settings</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-8">
            {{-- Admission Fee Configuration --}}
            <div>
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-file-invoice-dollar text-green-600"></i>
                    </span>
                    Admission Fee
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 ml-11">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fee Amount (KES)</label>
                        <input type="number" name="admission_fee_amount" value="{{ old('admission_fee_amount', $settings['admission_fee_amount'] ?? 2000) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            min="0" step="100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fee Label</label>
                        <input type="text" name="admission_fee_label" value="{{ old('admission_fee_label', $settings['admission_fee_label'] ?? 'Application/Admission Fee') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="admission_fee_description" value="{{ old('admission_fee_description', $settings['admission_fee_description'] ?? 'Non-refundable admission processing fee') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            {{-- Commitment Fee Configuration --}}
            <div>
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-handshake text-blue-600"></i>
                    </span>
                    Commitment Fee
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 ml-11">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fee Amount (KES)</label>
                        <input type="number" name="commitment_fee_amount" value="{{ old('commitment_fee_amount', $settings['commitment_fee_amount'] ?? 5000) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            min="0" step="100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fee Label</label>
                        <input type="text" name="commitment_fee_label" value="{{ old('commitment_fee_label', $settings['commitment_fee_label'] ?? 'Commitment Fee') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Days (after offer)</label>
                        <input type="number" name="commitment_fee_due_days" value="{{ old('commitment_fee_due_days', $settings['commitment_fee_due_days'] ?? 14) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            min="1" max="60">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="commitment_fee_description" value="{{ old('commitment_fee_description', $settings['commitment_fee_description'] ?? 'Fee to secure your admission offer') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            {{-- Currency Settings --}}
            <div>
                <h3 class="text-md font-semibold text-gray-900 mb-4 flex items-center">
                    <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-coins text-yellow-600"></i>
                    </span>
                    Currency
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-11">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency Code</label>
                        <input type="text" name="currency" value="{{ old('currency', $settings['currency'] ?? 'KES') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'KSh') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">M-PESA (Global Credentials)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shortcode</label>
                        <input type="text" name="mpesa_shortcode" value="{{ old('mpesa_shortcode', $settings['mpesa_shortcode'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Passkey</label>
                        <input type="password" name="mpesa_passkey" value="{{ old('mpesa_passkey', $settings['mpesa_passkey'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consumer Key</label>
                        <input type="text" name="mpesa_consumer_key" value="{{ old('mpesa_consumer_key', $settings['mpesa_consumer_key'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consumer Secret</label>
                        <input type="password" name="mpesa_consumer_secret" value="{{ old('mpesa_consumer_secret', $settings['mpesa_consumer_secret'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paybill/Till Number (shown to students)</label>
                        <input type="text" name="mpesa_paybill_number" value="{{ old('mpesa_paybill_number', $settings['mpesa_paybill_number'] ?? '') }}"
                            placeholder="174379"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Reference Format</label>
                        <input type="text" name="mpesa_account_reference" value="{{ old('mpesa_account_reference', $settings['mpesa_account_reference'] ?? 'ADM-{application_number}') }}"
                            placeholder="ADM-{application_number}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Use {application_number} as placeholder</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Payment Methods</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="mpesa_stk_enabled" value="0">
                        <input type="checkbox" name="mpesa_stk_enabled" value="1"
                            {{ old('mpesa_stk_enabled', $settings['mpesa_stk_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">M-PESA STK Push</span>
                            <span class="block text-xs text-gray-500">Instant payment via mobile</span>
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="paypal_enabled" value="0">
                        <input type="checkbox" name="paypal_enabled" value="1"
                            {{ old('paypal_enabled', $settings['paypal_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">PayPal</span>
                            <span class="block text-xs text-gray-500">International cards</span>
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="manual_payment_enabled" value="0">
                        <input type="checkbox" name="manual_payment_enabled" value="1"
                            {{ old('manual_payment_enabled', $settings['manual_payment_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Manual Payment</span>
                            <span class="block text-xs text-gray-500">Student enters M-PESA code</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Receipt Requirements</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 ml-4">
                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="payment_receipt_required" value="0">
                        <input type="checkbox" name="payment_receipt_required" value="1"
                            {{ old('payment_receipt_required', $settings['payment_receipt_required'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Receipt Upload Required</span>
                            <span class="block text-xs text-gray-500">Students must upload payment receipt</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>