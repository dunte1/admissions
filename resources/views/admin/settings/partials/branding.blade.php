<form action="{{ route('admin.settings.update', 'branding') }}" method="POST" enctype="multipart/form-data" id="branding-form">
    @csrf
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Branding Settings</h2>
                <p class="text-sm text-gray-500">Customize {{ $school ? 'school' : 'system' }} appearance</p>
            </div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                Save Changes
            </button>
        </div>

        <div class="space-y-6">
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Brand Colors</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="primary_color" 
                                value="{{ old('primary_color', $settings['primary_color'] ?? '#7C3AED') }}"
                                class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                            <input type="text" name="primary_color" 
                                value="{{ old('primary_color', $settings['primary_color'] ?? '#7C3AED') }}"
                                placeholder="#7C3AED"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="secondary_color" 
                                value="{{ old('secondary_color', $settings['secondary_color'] ?? '#10B981') }}"
                                class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                            <input type="text" name="secondary_color" 
                                value="{{ old('secondary_color', $settings['secondary_color'] ?? '#10B981') }}"
                                placeholder="#10B981"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Accent Color</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="accent_color" 
                                value="{{ old('accent_color', $settings['accent_color'] ?? '#F59E0B') }}"
                                class="w-12 h-10 rounded border border-gray-300 cursor-pointer">
                            <input type="text" name="accent_color" 
                                value="{{ old('accent_color', $settings['accent_color'] ?? '#F59E0B') }}"
                                placeholder="#F59E0B"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Logo & Images</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        <div class="mt-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-500 transition-colors">
                            <div class="space-y-1 text-center">
                                @if($school && $school->logo)
                                    <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="mx-auto h-20 w-auto">
                                @elseif(system_setting('system_logo'))
                                    <img src="{{ asset('storage/' . system_setting('system_logo')) }}" alt="Logo" class="mx-auto h-20 w-auto">
                                @else
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @endif
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="logo-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="logo-upload" name="logo" type="file" class="sr-only" accept="image/*" onchange="document.getElementById('branding-form').submit()">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                        <div class="mt-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-500 transition-colors">
                            <div class="space-y-1 text-center">
                                @if($school && $school->favicon)
                                    <img src="{{ asset('storage/' . $school->favicon) }}" alt="Favicon" class="mx-auto h-12 w-12 object-contain">
                                @elseif(system_setting('favicon'))
                                    <img src="{{ asset('storage/' . system_setting('favicon')) }}" alt="Favicon" class="mx-auto h-12 w-12 object-contain">
                                @else
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M24 4l-4 4m0 0L4 24m4-4v32m28-20l-4-4m0 0l16 16m-4-4V8m0 0H12m12 0l-4 4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @endif
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="favicon-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="favicon-upload" name="favicon" type="file" class="sr-only" accept="image/*,.ico" onchange="document.getElementById('branding-form').submit()">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">ICO, PNG up to 512KB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-medium text-gray-900 mb-4">Footer</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Custom Footer Text</label>
                        <input type="text" name="footer_text" 
                            value="{{ old('footer_text', $settings['footer_text'] ?? '© ' . date('Y') . ' ' . ($school ? $school->name : system_setting('system_name', config('app.name'))) . '. Powered by Duncowebsolutions') }}"
                            placeholder="© 2024 Your Institution. All rights reserved."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Leave empty to use default footer with "Powered by Duncowebsolutions"</p>
                    </div>
                    @if($school)
                    <div class="flex items-center">
                        <input type="checkbox" name="enable_white_label" id="enable_white_label" 
                            value="1" 
                            {{ old('enable_white_label', $school->enable_white_label ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="enable_white_label" class="ml-2 block text-sm text-gray-700">
                            Enable White Label (Hide footer completely)
                        </label>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>