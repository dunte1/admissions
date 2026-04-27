<form action="{{ route('admin.settings.update', 'security') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Security Settings</h2>
                <p class="text-sm text-gray-500">Configure security policies and access controls</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-6">
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Password Requirements</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Password Length</label>
                        <input type="number" name="password_min_length" 
                            value="{{ old('password_min_length', $settings['password_min_length'] ?? 8) }}"
                            min="8" max="32"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="password_require_uppercase" value="0">
                        <input type="checkbox" name="password_require_uppercase" value="1"
                            {{ old('password_require_uppercase', $settings['password_require_uppercase'] ?? '1') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Require at least one uppercase letter</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="password_require_lowercase" value="0">
                        <input type="checkbox" name="password_require_lowercase" value="1"
                            {{ old('password_require_lowercase', $settings['password_require_lowercase'] ?? '1') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Require at least one lowercase letter</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="password_require_numbers" value="0">
                        <input type="checkbox" name="password_require_numbers" value="1"
                            {{ old('password_require_numbers', $settings['password_require_numbers'] ?? '1') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Require at least one number</span>
                    </label>

                    <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="password_require_special" value="0">
                        <input type="checkbox" name="password_require_special" value="1"
                            {{ old('password_require_special', $settings['password_require_special'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3 text-sm text-gray-700">Require at least one special character</span>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Session & Login Security</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Session Timeout (minutes)</label>
                        <input type="number" name="session_timeout" 
                            value="{{ old('session_timeout', $settings['session_timeout'] ?? 60) }}"
                            min="15" max="480"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Login Attempts</label>
                        <input type="number" name="max_login_attempts" 
                            value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}"
                            min="3" max="10"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lockout Duration (minutes)</label>
                        <input type="number" name="lockout_duration" 
                            value="{{ old('lockout_duration', $settings['lockout_duration'] ?? 15) }}"
                            min="1" max="60"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Additional Security</h3>
                <div class="space-y-4">
                    <label class="flex items-center p-4 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="hidden" name="enable_2fa" value="0">
                        <input type="checkbox" name="enable_2fa" value="1"
                            {{ old('enable_2fa', $settings['enable_2fa'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="ml-3">
                            <span class="block text-sm font-medium text-gray-700">Enable Two-Factor Authentication</span>
                            <span class="block text-xs text-gray-500">Require 2FA for admin users (coming soon)</span>
                        </span>
                    </label>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">IP Whitelist (one per line, optional)</label>
                        <textarea name="ip_whitelist" rows="4"
                            placeholder="192.168.1.1&#10;10.0.0.0/8"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('ip_whitelist', $settings['ip_whitelist'] ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Leave empty to allow all IPs. Admin access will be restricted to these IPs.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>