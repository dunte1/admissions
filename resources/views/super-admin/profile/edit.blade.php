@extends('layouts.super-admin')

@section('header', 'Profile Management')
@section('breadcrumb', 'Manage your account')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Profile Management</h1>
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
                    <h3 class="font-semibold text-gray-900">Profile Menu</h3>
                </div>
                <nav class="divide-y divide-gray-100">
                    <a href="#profile" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors border-l-4 border-purple-600 bg-purple-50 text-purple-700">
                        <i class="fas fa-user w-5 mr-3"></i>
                        Profile
                    </a>
                    <a href="#security" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-shield-alt w-5 mr-3"></i>
                        Security
                    </a>
                    <a href="#notifications" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-bell w-5 mr-3"></i>
                        Notifications
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

                <form action="{{ route('super-admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="shrink-0">
                            <div class="h-24 w-24 rounded-full bg-purple-100 flex items-center justify-center overflow-hidden" id="photo-preview-container">
                                <x-user-avatar :user="$user" :size="96" class="" />
                            </div>
                        </div>
                        <div>
                            <label class="block">
                                <span class="sr-only">Choose profile photo</span>
                                <input type="file" name="photo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" onchange="previewPhoto(this)"/>
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

            <div id="security" class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Security</h2>
                    <p class="text-sm text-gray-500">Manage your account security</p>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-medium text-gray-900 mb-4">Change Password</h3>
                    <form action="{{ route('super-admin.profile.update-password') }}" method="POST" class="space-y-4">
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
                    
                    @if($user->hasTwoFactorEnabled())
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-green-600 text-2xl mr-3"></i>
                                <div>
                                    <p class="font-medium text-green-800">2FA is Enabled</p>
                                    <p class="text-sm text-green-600">Your account is protected</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Active</span>
                        </div>
                    @else
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-gray-400 text-2xl mr-3"></i>
                                <div>
                                    <p class="font-medium text-gray-700">2FA is Not Enabled</p>
                                    <p class="text-sm text-gray-500">Add extra security to your account</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <a href="{{ route('two-factor.setup') }}" class="inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                        {{ $user->hasTwoFactorEnabled() ? 'Manage 2FA' : 'Enable 2FA' }}
                    </a>
                </div>

                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="font-medium text-gray-900 mb-4">Active Sessions</h3>
                    
                    <form action="{{ route('sessions.logout-all') }}" method="POST">
                        @csrf
                        <details class="mt-4">
                            <summary class="cursor-pointer text-red-600 hover:text-red-800 text-sm font-medium">
                                Logout from all devices
                            </summary>
                            <div class="mt-3 p-4 bg-red-50 rounded-lg">
                                <p class="text-sm text-red-700 mb-3">This will log you out from all devices. Enter your password to confirm.</p>
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

            <div id="notifications" class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Notification History</h2>
                    <p class="text-sm text-gray-500">View your recent notifications</p>
                </div>
                
                <a href="{{ route('super-admin.profile.notifications') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-bell mr-2"></i>
                    View All Notifications
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var container = document.getElementById('photo-preview-container');
            container.innerHTML = '<img src="' + e.target.result + '" class="h-24 w-24 rounded-full object-cover">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
