@extends('layouts.admin')

@section('page-title', 'Profile Settings')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Settings Menu</h3>
                </div>
                <nav class="divide-y divide-gray-100">
                    <a href="{{ route('admin.profile.edit') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-user w-5 mr-3"></i>
                        Profile
                    </a>
                    <a href="{{ route('admin.profile.notifications') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-bell w-5 mr-3"></i>
                        Notifications
                    </a>
                    <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors border-l-4 border-purple-600 bg-purple-50 text-purple-700">
                        <i class="fas fa-cog w-5 mr-3"></i>
                        Preferences
                    </a>
                </nav>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Preferences</h2>
                    <p class="text-sm text-gray-500">Customize your experience</p>
                </div>

                <form action="{{ route('admin.profile.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Language & Region</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="preferred_language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                    <select name="preferred_language" id="preferred_language" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="en" {{ old('preferred_language', $user->preferred_language ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                        <option value="sw" {{ old('preferred_language', $user->preferred_language ?? 'en') === 'sw' ? 'selected' : '' }}>Swahili</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Notifications</h3>
                            <div class="space-y-4">
                                <label class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">Email notifications</span>
                                    <input type="checkbox" name="email_notifications" value="1" {{ ($user->getSettings('email_notifications', '1') === '1') ? 'checked' : '' }} class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                </label>
                                <label class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">SMS notifications</span>
                                    <input type="checkbox" name="sms_notifications" value="1" {{ ($user->getSettings('sms_notifications', '0') === '1') ? 'checked' : '' }} class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                </label>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-900 mb-4">Appearance</h3>
                            <div class="space-y-4">
                                <label class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">Dark mode</span>
                                    <input type="checkbox" name="dark_mode" value="1" {{ ($user->dark_mode ?? false) ? 'checked' : '' }} class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Preferences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
