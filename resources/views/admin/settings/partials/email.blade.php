<form action="{{ route('admin.settings.update', 'email') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Email & SMS Settings</h2>
                <p class="text-sm text-gray-500">Configure notification channels</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-6">
            <div>
                <h3 class="text-md font-medium text-gray-900 mb-4">Notification Channels</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="email_notifications_enabled" value="0">
                        <input type="checkbox" name="email_notifications_enabled" value="1"
                            {{ old('email_notifications_enabled', $settings['email_notifications_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Email Notifications</span>
                            <span class="block text-xs text-gray-500">Send email notifications to users</span>
                        </span>
                    </label>

                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="sms_notifications_enabled" value="0">
                        <input type="checkbox" name="sms_notifications_enabled" value="1"
                            {{ old('sms_notifications_enabled', $settings['sms_notifications_enabled'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">SMS Notifications</span>
                            <span class="block text-xs text-gray-500">Send SMS notifications via API</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">SMTP Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mail Driver</label>
                        <select name="mail_mailer" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="mail" {{ ($settings['mail_mailer'] ?? 'smtp') === 'mail' ? 'selected' : '' }}>Mail</option>
                            <option value="sendmail" {{ ($settings['mail_mailer'] ?? 'smtp') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                        <input type="text" name="mail_host" 
                            value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                            placeholder="smtp.mailtrap.io"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                        <input type="number" name="mail_port" 
                            value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}"
                            placeholder="587"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="mail_username" 
                            value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="mail_password" 
                            value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                        <select name="mail_encryption" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="null" {{ ($settings['mail_encryption'] ?? 'tls') === 'null' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                        <input type="email" name="mail_from_address" 
                            value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                            placeholder="noreply@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                        <input type="text" name="mail_from_name" 
                            value="{{ old('mail_from_name', $settings['mail_from_name'] ?? $settings['app_name'] ?? 'Admissions Portal') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">SMS Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMS API Key</label>
                        <input type="password" name="sms_api_key" 
                            value="{{ old('sms_api_key', $settings['sms_api_key'] ?? '') }}"
                            placeholder="Your SMS API Key"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sender ID</label>
                        <input type="text" name="sms_sender_id" 
                            value="{{ old('sms_sender_id', $settings['sms_sender_id'] ?? '') }}"
                            placeholder="ADMISSIONS"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>