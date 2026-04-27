@extends('layouts.student')

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
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Preferences</h3>
                </div>
                <nav class="divide-y divide-gray-100">
                    <a href="#profile" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors border-l-4 border-purple-600 bg-purple-50 text-purple-700">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>
                    <a href="#notifications" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        Notifications
                    </a>
                    <a href="#appearance" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        Appearance
                    </a>
                    <a href="#security" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Security
                    </a>
                </nav>
            </div>
        </div>

        <div class="lg:col-span-3 space-y-6">
            <div id="profile" class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Profile Information</h2>
                        <p class="text-sm text-gray-500">Update your personal information</p>
                    </div>
                </div>

                <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="shrink-0">
                            <div class="h-24 w-24 rounded-full overflow-hidden">
                                <x-user-avatar :user="$user" :size="96" class="" />
                            </div>
                        </div>
                        <div>
                            <label class="block">
                                <span class="sr-only">Choose profile photo</span>
                                <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"/>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">JPG, GIF or PNG. Max size 2MB.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Language</label>
                            <select name="preferred_language" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="en" {{ ($user->preferred_language ?? 'en') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="sw" {{ ($user->preferred_language ?? 'en') === 'sw' ? 'selected' : '' }}>Swahili</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <div id="notifications" class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Notification Preferences</h2>
                    <p class="text-sm text-gray-500">Choose how you want to receive notifications</p>
                </div>

                <form action="{{ route('student.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">Email Notifications</p>
                                    <p class="text-sm text-gray-500">Receive updates via email</p>
                                </div>
                            </div>
                            <input type="hidden" name="email_notifications" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" value="1" 
                                    {{ old('email_notifications', $user->getSetting('email_notifications', '1')) == '1' ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900">SMS Notifications</p>
                                    <p class="text-sm text-gray-500">Receive updates via SMS</p>
                                </div>
                            </div>
                            <input type="hidden" name="sms_notifications" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sms_notifications" value="1"
                                    {{ old('sms_notifications', $user->getSetting('sms_notifications', '0')) == '1' ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Save Preferences
                        </button>
                    </div>
                </form>
            </div>

            <div id="appearance" class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Appearance</h2>
                    <p class="text-sm text-gray-500">Customize the look of your dashboard</p>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Dark Mode</p>
                            <p class="text-sm text-gray-500">Switch between light and dark themes</p>
                        </div>
                    </div>
                    <form action="{{ route('student.profile.dark-mode') }}" method="POST">
                        @csrf
                        <input type="hidden" name="dark_mode" value="{{ $user->dark_mode ? '0' : '1' }}">
                        <button type="submit" class="relative inline-flex items-center cursor-pointer">
                            <div class="w-11 h-6 {{ $user->dark_mode ? 'bg-purple-600' : 'bg-gray-200' }} rounded-full transition-colors">
                                <div class="absolute top-[2px] {{ $user->dark_mode ? 'right-[2px]' : 'left-[2px]' }} bg-white w-5 h-5 rounded-full shadow transition-transform"></div>
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            <div id="security" class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Security</h2>
                    <p class="text-sm text-gray-500">Manage your account security</p>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-medium text-gray-900 mb-4">Change Password</h3>
                    <form action="{{ route('student.profile.update-password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                            <input type="password" name="current_password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            </div>
                        </div>

                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            Update Password
                        </button>
                    </form>
                </div>

                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="font-medium text-gray-900 mb-4">Two-Factor Authentication</h3>
                    
                    @if(session('recovery_codes'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="font-semibold text-green-800 mb-2">Recovery Codes Generated</p>
                            <p class="text-sm text-green-700 mb-3">Store these codes in a safe place:</p>
                            <div class="grid grid-cols-2 gap-2 bg-white p-3 rounded border border-green-200">
                                @foreach(session('recovery_codes') as $code)
                                    <span class="font-mono text-sm text-green-800">{{ $code }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($user->hasTwoFactorEnabled())
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg mb-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-green-800">2FA is Enabled</p>
                                    <p class="text-sm text-green-600">Your account is protected with two-factor authentication</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Active</span>
                        </div>
                        
                        <form action="{{ route('two-factor.regenerate') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="password" name="password" placeholder="Confirm with password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <button type="submit" class="mt-3 text-purple-600 hover:text-purple-800 text-sm font-medium">
                                Regenerate Recovery Codes
                            </button>
                        </form>
                        
                        <form action="{{ route('two-factor.disable') }}" method="POST">
                            @csrf
                            <details class="mt-4">
                                <summary class="cursor-pointer text-red-600 hover:text-red-800 text-sm font-medium">
                                    Disable Two-Factor Authentication
                                </summary>
                                <div class="mt-3 p-4 bg-red-50 rounded-lg">
                                    <p class="text-sm text-red-700 mb-3">Are you sure? Your account will be less secure.</p>
                                    <input type="password" name="password" placeholder="Enter password to confirm" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3">
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                        Disable 2FA
                                    </button>
                                </div>
                            </details>
                        </form>
                    @else
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-700">2FA is Not Enabled</p>
                                    <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-gray-200 text-gray-600 rounded-full text-sm font-medium">Inactive</span>
                        </div>
                        
                        <form action="{{ route('two-factor.setup') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                                Enable Two-Factor Authentication
                            </button>
                        </form>
                    @endif
                </div>

                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="font-medium text-gray-900 mb-4">Active Sessions</h3>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-700">This Device</p>
                                <p class="text-sm text-gray-500">Current active session</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Current</span>
                    </div>

                    <form action="{{ route('sessions.logout-all') }}" method="POST">
                        @csrf
                        <details class="mt-4">
                            <summary class="cursor-pointer text-red-600 hover:text-red-800 text-sm font-medium">
                                Logout from all devices
                            </summary>
                            <div class="mt-3 p-4 bg-red-50 rounded-lg">
                                <p class="text-sm text-red-700 mb-3">This will log you out from all devices except the current one. Enter your password to confirm.</p>
                                <input type="password" name="password" placeholder="Enter your password" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3">
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                    Logout All Devices
                                </button>
                            </div>
                        </details>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection