<form action="{{ route('super-admin.settings.update', 'sms') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">SMS Configuration</h2>
                <p class="text-sm text-gray-500">SMS gateway settings for notifications</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="flex items-center p-4 bg-gray-50 rounded-lg mb-6">
            <input type="hidden" name="sms_notifications_enabled" value="0">
            <input type="checkbox" name="sms_notifications_enabled" id="sms_enabled" value="1"
                {{ old('sms_notifications_enabled', $settings['sms_notifications_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
            <label for="sms_enabled" class="ml-3">
                <span class="block text-sm font-medium text-gray-900">Enable SMS Notifications</span>
                <span class="block text-sm text-gray-500">Send SMS notifications to users</span>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SMS API Provider</label>
                <select name="sms_provider" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="twilio">Twilio</option>
                    <option value=" Africa's Talking">Africa's Talking</option>
                    <option value="bulk_sms">Bulk SMS</option>
                    <option value="custom">Custom API</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sender ID</label>
                <input type="text" name="sms_sender_id" value="{{ old('sms_sender_id', $settings['sms_sender_id'] ?? '') }}"
                    placeholder="ADMISSIONS"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">API Key / Token</label>
            <input type="password" name="sms_api_key" value="{{ old('sms_api_key', $settings['sms_api_key'] ?? '') }}"
                placeholder="Your SMS API Key"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>Tip:</strong> Schools can override the Sender ID in their own settings, but must use their own API credentials for country-specific regulations.
            </p>
        </div>
    </div>
</form>