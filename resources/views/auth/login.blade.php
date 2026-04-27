<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', (isset($selectedSchool) && $selectedSchool ? $selectedSchool->name . ' - ' : '') . app_name() . ' - Admission Portal')</title>
    <link rel="icon" href="{{ isset($selectedSchool) && $selectedSchool && $selectedSchool->favicon ? asset('storage/' . $selectedSchool->favicon) : app_favicon() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        html, body { height: 100%; margin: 0; padding: 0; overflow: hidden; }
        
        :root {
            --primary: {{ isset($selectedSchool) && $selectedSchool ? ($selectedSchool->primary_color ?? primary_color()) : primary_color() }};
            --secondary: {{ isset($selectedSchool) && $selectedSchool ? ($selectedSchool->secondary_color ?? system_setting('secondary_color', '#10B981')) : system_setting('secondary_color', '#10B981') }};
        }
        
        .brand-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .input-focus:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 20%, transparent);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .tab-active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex items-center justify-center bg-gradient-to-br from-gray-100 via-gray-50 to-gray-100">
    <div class="w-full h-full flex items-center justify-center p-3 sm:p-4">
        <div class="w-full max-w-sm sm:max-w-md">
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6">
                <div class="text-center mb-4">
                    @if(isset($selectedSchool) && $selectedSchool && $selectedSchool->logo)
                        <img src="{{ asset('storage/' . $selectedSchool->logo) }}" alt="{{ $selectedSchool->name }}" class="w-12 h-12 sm:w-16 sm:h-16 mx-auto object-contain rounded-xl shadow-sm mb-2">
                    @elseif(system_setting('system_logo'))
                        <img src="{{ asset('storage/' . system_setting('system_logo')) }}" alt="{{ app_name() }}" class="w-12 h-12 sm:w-16 sm:h-16 mx-auto object-contain rounded-xl shadow-sm mb-2">
                    @else
                        <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto rounded-xl flex items-center justify-center shadow-sm mb-2 brand-gradient">
                            <i class="fas fa-graduation-cap text-white text-lg sm:text-2xl"></i>
                        </div>
                    @endif
                    <h1 class="text-xl sm:text-2xl font-bold" style="color: var(--primary)">{{ isset($selectedSchool) && $selectedSchool ? $selectedSchool->name : app_name() }}</h1>
                    <p class="text-gray-500 text-xs sm:text-sm mt-0.5">{{ system_setting('tagline', 'Streamline Admissions') }}</p>
                </div>
                
                @if(isset($selectedSchool) && $selectedSchool)
                    <div class="bg-gray-50 rounded-lg p-2 mb-4 text-center">
                        <span class="text-xs font-medium" style="color: var(--primary)">{{ $selectedSchool->name }}</span>
                    </div>
                @endif
                
                <div class="mb-4">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-1 text-center">@lang('labels.sign_in')</h2>
                    <p class="text-gray-500 text-xs sm:text-sm text-center mb-3">@lang('labels.sign_in_desc')</p>
                </div>
                
                @if(session('success'))
                    <div class="mb-3 bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-xs flex items-center">
                        <i class="fas fa-check-circle mr-2 text-green-500"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-3 bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-xs flex items-center">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="flex border-b border-gray-200 mb-4">
                    <button type="button" onclick="showLogin()" id="login-tab" class="flex-1 pb-2 text-xs sm:text-sm font-medium transition-all tab-active">
                        @lang('labels.sign_in')
                    </button>
                    <button type="button" onclick="showRegister()" id="register-tab" class="flex-1 pb-2 text-xs sm:text-sm font-medium text-gray-400 hover:text-gray-600 transition-all">
                        @lang('labels.register')
                    </button>
                </div>
                
                <div id="login-form-container">
                    <form method="POST" action="{{ route('login') }}" id="login-form">
                        @csrf
                        @if(isset($selectedSchool) && $selectedSchool)
                            <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
                        @endif
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">@lang('labels.email')</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                </div>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                    class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg input-focus focus:outline-none @error('email') border-red-500 bg-red-50 @enderror"
                                    placeholder="you@example.com">
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">@lang('labels.password')</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 text-xs"></i>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="w-full pl-9 pr-10 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg input-focus focus:outline-none @error('password') border-red-500 bg-red-50 @enderror"
                                    placeholder="@lang('labels.password')">
                                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i id="eye-icon" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex items-center justify-between mb-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300">
                                <span class="ml-1.5 text-xs text-gray-600">@lang('labels.remember_me')</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs font-medium" style="color: var(--primary)">
                                @lang('labels.forgot_password')
                            </a>
                        </div>
                        
                        <button type="submit" class="w-full btn-primary text-white py-2 rounded-lg font-medium text-sm shadow-md">
                            @lang('labels.sign_in')
                        </button>
                    </form>
                </div>
                
                <div id="register-form-container" class="hidden">
                    <form method="POST" action="{{ route('register') }}" id="register-form">
                        @csrf
                        @if(isset($selectedSchool) && $selectedSchool)
                            <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
                        @endif
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">@lang('labels.full_name')</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg input-focus focus:outline-none @error('name') border-red-500 bg-red-50 @enderror"
                                    placeholder="@lang('labels.full_name_placeholder')">
                            </div>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">@lang('labels.email')</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 text-xs"></i>
                                </div>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg input-focus focus:outline-none @error('email') border-red-500 bg-red-50 @enderror"
                                    placeholder="you@example.com">
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">@lang('labels.password')</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 text-xs"></i>
                                </div>
                                <input type="password" name="password" id="reg-password" required
                                    class="w-full pl-9 pr-10 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg input-focus focus:outline-none @error('password') border-red-500 bg-red-50 @enderror"
                                    placeholder="@lang('labels.password_min')">
                                <button type="button" onclick="toggleRegPassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i id="reg-eye-icon" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">@lang('labels.confirm_password')</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 text-xs"></i>
                                </div>
                                <input type="password" name="password_confirmation" id="reg-confirm-password" required
                                    class="w-full pl-9 pr-10 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg input-focus focus:outline-none"
                                    placeholder="@lang('labels.confirm_password')">
                                <button type="button" onclick="toggleRegConfirmPassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i id="reg-confirm-eye-icon" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full btn-primary text-white py-2 rounded-lg font-medium text-sm shadow-md">
                            @lang('labels.register')
                        </button>
                    </form>
                </div>
                
                <div class="mt-4 pt-3 border-t border-gray-100 text-center">
                    <div class="flex items-center justify-center space-x-3 mb-2">
                        <a href="{{ route('lang', 'en') }}" class="text-xs {{ app()->getLocale() == 'en' ? 'font-semibold' : 'text-gray-400' }}" style="{{ app()->getLocale() == 'en' ? 'color: var(--primary)' : '' }}">
                            English
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('lang', 'sw') }}" class="text-xs {{ app()->getLocale() == 'sw' ? 'font-semibold' : 'text-gray-400' }}" style="{{ app()->getLocale() == 'sw' ? 'color: var(--primary)' : '' }}">
                            Kiswahili
                        </a>
                    </div>
                    <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 hover:text-gray-600 text-xs">
                        <i class="fas fa-arrow-left mr-1"></i>@lang('labels.back_home')
                    </a>
                </div>
            </div>
            
            @if(app_footer())
                <p class="text-center text-gray-400 text-xs mt-3">
                    {!! app_footer() !!}
                </p>
            @endif
        </div>
    </div>
    
    <script>
        function togglePassword() {
            var password = document.getElementById('password');
            var eyeIcon = document.getElementById('eye-icon');
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        
        function toggleRegPassword() {
            var password = document.getElementById('reg-password');
            var eyeIcon = document.getElementById('reg-eye-icon');
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        
        function toggleRegConfirmPassword() {
            var password = document.getElementById('reg-confirm-password');
            var eyeIcon = document.getElementById('reg-confirm-eye-icon');
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        
        function showLogin() {
            document.getElementById('login-form-container').classList.remove('hidden');
            document.getElementById('register-form-container').classList.add('hidden');
            document.getElementById('login-tab').classList.add('tab-active');
            document.getElementById('register-tab').classList.remove('tab-active');
        }
        
        function showRegister() {
            document.getElementById('login-form-container').classList.add('hidden');
            document.getElementById('register-form-container').classList.remove('hidden');
            document.getElementById('register-tab').classList.add('tab-active');
            document.getElementById('login-tab').classList.remove('tab-active');
        }
        
        @if(isset($showRegister) && $showRegister)
            document.addEventListener('DOMContentLoaded', function() {
                showRegister();
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>