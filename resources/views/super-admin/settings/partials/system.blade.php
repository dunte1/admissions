<form action="{{ route('super-admin.settings.update', 'system') }}" method="POST">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">System Configuration</h2>
                <p class="text-sm text-gray-500">Basic application settings</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Application Name</label>
                <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? 'Admission Portal') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Application URL</label>
                <input type="url" name="app_url" value="{{ old('app_url', $settings['app_url'] ?? url('/')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Environment</label>
                <select name="app_env" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="production" {{ (env('APP_ENV') ?? 'production') === 'production' ? 'selected' : '' }}>Production</option>
                    <option value="local" {{ (env('APP_ENV') ?? 'production') === 'local' ? 'selected' : '' }}>Local</option>
                    <option value="development" {{ (env('APP_ENV') ?? 'production') === 'development' ? 'selected' : '' }}>Development</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Warning: Changing environment requires .env modification</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Debug Mode</label>
                <select name="app_debug" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="0">Disabled (Production)</option>
                    <option value="1" {{ config('app.debug') ? 'selected' : '' }}>Enabled (Development)</option>
                </select>
            </div>
        </div>

        <div class="border-t border-gray-200 mt-6 pt-6">
            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-yellow-800">Maintenance Mode</p>
                        <p class="text-sm text-yellow-600">Only admins can access the site when enabled</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" value="1"
                        {{ app()->isDownForMaintenance() ? 'checked' : '' }}
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                </label>
            </div>
        </div>
    </div>
</form>