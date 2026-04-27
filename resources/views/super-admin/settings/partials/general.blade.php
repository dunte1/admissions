<form action="{{ route('super-admin.settings.update', 'general') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">General Settings</h2>
                <p class="text-sm text-gray-500">Default settings for all schools</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">System Name</label>
                <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? 'Admission Portal') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">System URL</label>
                <input type="url" name="app_url" value="{{ old('app_url', $settings['app_url'] ?? url('/')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Timezone</label>
                <select name="timezone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="Africa/Nairobi" {{ ($settings['timezone'] ?? '') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                    <option value="Africa/Kampala" {{ ($settings['timezone'] ?? '') === 'Africa/Kampala' ? 'selected' : '' }}>Africa/Kampala (EAT)</option>
                    <option value="Africa/Dar_es_Salaam" {{ ($settings['timezone'] ?? '') === 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Africa/Dar_es_Salaam (EAT)</option>
                    <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Language</label>
                <select name="locale" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="en" {{ ($settings['locale'] ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                    <option value="sw" {{ ($settings['locale'] ?? 'en') === 'sw' ? 'selected' : '' }}>Swahili</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Default Currency</label>
                <select name="currency" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="KES" {{ ($settings['currency'] ?? 'KES') === 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                    <option value="USD" {{ ($settings['currency'] ?? 'KES') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                    <option value="EUR" {{ ($settings['currency'] ?? 'KES') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                    <option value="TZS" {{ ($settings['currency'] ?? 'KES') === 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                    <option value="UGX" {{ ($settings['currency'] ?? 'KES') === 'UGX' ? 'selected' : '' }}>UGX - Ugandan Shilling</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? 'KSh') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>
    </div>
</form>