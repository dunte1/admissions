@extends('layouts.student')

@section('title', 'My Profile')

@push('styles')
<style>
    .completion-ring {
        transform: rotate(-90deg);
    }
    .completion-ring circle {
        transition: stroke-dashoffset 0.5s ease-in-out;
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('labels.edit_profile') }}</h1>
        <p class="text-gray-600 mt-2">Manage your account settings and profile information</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Overview --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                {{-- Profile Card --}}
                <div class="text-center mb-6">
                    <div class="w-24 h-24 rounded-full overflow-hidden flex items-center justify-center mx-auto mb-4 shadow-lg bg-gradient-to-br from-[#00008B] to-[#1e40af]">
                        <x-user-avatar :user="$user" :size="96" class="" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $user->fullName() }}</h3>
                    <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                    <p class="text-xs text-gray-400 mt-1 capitalize">{{ ucfirst($user->role) }}</p>
                </div>

                {{-- Profile Completion --}}
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 text-center">Profile Completion</h4>
                    <div class="relative w-32 h-32 mx-auto mb-4">
                        <svg class="completion-ring w-full h-full">
                            <circle cx="64" cy="64" r="56" stroke="#e5e7eb" stroke-width="12" fill="none"/>
                            <circle cx="64" cy="64" r="56" stroke="#10b981" stroke-width="12" fill="none"
                                stroke-dasharray="352"
                                stroke-dashoffset="{{ 352 - (352 * auth()->user()->getProfileCompletion() / 100) }}"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900">{{ auth()->user()->getProfileCompletion() }}%</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 text-center mb-4">Complete your profile to unlock all features</p>
                    
                    {{-- Profile Checklist --}}
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-{{ $user->first_name ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} w-5"></i>
                            <span class="{{ $user->first_name ? 'text-gray-700' : 'text-gray-400' }}">First Name</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-{{ $user->last_name ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} w-5"></i>
                            <span class="{{ $user->last_name ? 'text-gray-700' : 'text-gray-400' }}">Last Name</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-{{ $user->email ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} w-5"></i>
                            <span class="{{ $user->email ? 'text-gray-700' : 'text-gray-400' }}">Email</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-{{ $user->phone ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} w-5"></i>
                            <span class="{{ $user->phone ? 'text-gray-700' : 'text-gray-400' }}">Phone Number</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Personal Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user text-[#00008B] mr-2"></i>
                        {{ __('labels.personal_information') }}
                    </h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.first_name') }} *</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.last_name') }} *</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.email') }} *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.phone') }}</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">+254</span>
                                    <input type="tel" name="phone" value="{{ old('phone', $user->phone ? str_replace('254', '', $user->phone) : '') }}"
                                        placeholder="712345678"
                                        class="w-full pl-14 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Enter your phone number without the country code</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.preferred_language') }}</label>
                                <select name="preferred_language" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                                    <option value="en" {{ $user->preferred_language == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="sw" {{ $user->preferred_language == 'sw' ? 'selected' : '' }}>Kiswahili</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full md:w-auto bg-[#00008B] text-white px-8 py-3 rounded-lg hover:bg-[#1e40af] font-medium shadow-lg shadow-blue-200">
                                <i class="fas fa-save mr-2"></i>
                                {{ __('labels.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-lock text-[#00008B] mr-2"></i>
                        {{ __('labels.change_password') }}
                    </h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('student.profile.update-password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.current_password') }} *</label>
                            <input type="password" name="current_password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.new_password') }} *</label>
                                <input type="password" name="password" required minlength="8"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.confirm_password') }} *</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Password must be at least 8 characters and include a mix of letters and numbers.
                            </p>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full md:w-auto bg-gray-800 text-white px-8 py-3 rounded-lg hover:bg-gray-900 font-medium">
                                <i class="fas fa-key mr-2"></i>
                                {{ __('labels.update_password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Notification Preferences --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bell text-[#00008B] mr-2"></i>
                        Notification Preferences
                    </h2>
                </div>
                <div class="p-6">
                    <form action="#" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Email Notifications</p>
                                    <p class="text-sm text-gray-500">Receive updates via email</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">SMS Notifications</p>
                                    <p class="text-sm text-gray-500">Receive updates via SMS (MPESA)</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Application Status Updates</p>
                                    <p class="text-sm text-gray-500">Get notified when your application status changes</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Payment Reminders</p>
                                    <p class="text-sm text-gray-500">Receive reminders about pending payments</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="w-full md:w-auto bg-gray-800 text-white px-8 py-3 rounded-lg hover:bg-gray-900 font-medium">
                                <i class="fas fa-save mr-2"></i>
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Account Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-red-200 bg-red-50">
                    <h2 class="text-lg font-semibold text-red-900 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        Danger Zone
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-gray-900">Delete Account</p>
                            <p class="text-sm text-gray-500">Permanently delete your account and all data</p>
                        </div>
                        <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium text-sm">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
