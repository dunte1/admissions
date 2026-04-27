<form action="{{ route('super-admin.settings.update', 'mail') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Mail Configuration</h2>
                <p class="text-sm text-gray-500">Global email settings for all schools</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mail Driver</label>
                <select name="mail_mailer" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="mail" {{ ($settings['mail_mailer'] ?? 'smtp') === 'mail' ? 'selected' : '' }}>Mail</option>
                    <option value="sendmail" {{ ($settings['mail_mailer'] ?? 'smtp') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                    <option value="log" {{ ($settings['mail_mailer'] ?? 'smtp') === 'log' ? 'selected' : '' }}>Log (Testing)</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                    placeholder="smtp.mailtrap.io"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                <select name="mail_encryption" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ ($settings['mail_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="null" {{ ($settings['mail_encryption'] ?? 'tls') === 'null' ? 'selected' : '' }}>None</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                    placeholder="noreply@example.com"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}"
                    placeholder="Admission Portal"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-700">
                <strong>Note:</strong> These are default mail settings. Schools can override these in their own settings.
            </p>
        </div>
    </div>
</form>