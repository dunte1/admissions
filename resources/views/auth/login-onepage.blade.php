<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
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
        * { font-family: 'Inter', sans-serif; }
        
        :root {
            --primary: {{ isset($selectedSchool) && $selectedSchool ? ($selectedSchool->primary_color ?? primary_color()) : primary_color() }};
            --secondary: {{ isset($selectedSchool) && $selectedSchool ? ($selectedSchool->secondary_color ?? system_setting('secondary_color', '#10B981')) : system_setting('secondary_color', '#10B981') }};
        }
        
        .brand-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .navy-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        
        .input-focus:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary) 20%, transparent);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px color-mix(in srgb, var(--primary) 40%, transparent);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .tab-active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }
        
        .school-badge {
            background: color-mix(in srgb, var(--primary) 10%, transparent);
            border: 1px solid color-mix(in srgb, var(--primary) 30%, transparent);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md px-4 py-8 fade-in">
        <div class="text-center mb-8">
            @if(isset($selectedSchool) && $selectedSchool && $selectedSchool->logo)
                <img src="{{ asset('storage/' . $selectedSchool->logo) }}" alt="{{ $selectedSchool->name }}" class="w-20 h-20 mx-auto object-contain rounded-2xl shadow-lg mb-4">
            @elseif(system_setting('system_logo'))
                <img src="{{ asset('storage/' . system_setting('system_logo')) }}" alt="{{ app_name() }}" class="w-20 h-20 mx-auto object-contain rounded-2xl shadow-lg mb-4">
            @else
                <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center shadow-lg mb-4 brand-gradient">
                    <i class="fas fa-graduation-cap text-3xl text-white"></i>
                </div>
            @endif
            <h1 class="text-3xl font-bold" style="color: var(--primary)">{{ isset($selectedSchool) && $selectedSchool ? $selectedSchool->name : app_name() }}</h1>
            <p class="text-gray-500 text-sm">{{ system_setting('tagline', 'Streamline Admissions') }}</p>
        </div>
        
        @if(isset($selectedSchool) && $selectedSchool)
            <div class="school-badge rounded-lg p-3 mb-6 text-center">
                <i class="fas fa-building mr-2" style="color: var(--primary)"></i>
                <span class="font-medium text-sm" style="color: var(--primary)">{{ $selectedSchool->name }}</span>
            </div>
        @endif
        
        <div class="bg-white rounded-2xl navy-shadow p-6 sm:p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="flex justify-center mb-6 border-b border-gray-200">
                <button type="button" onclick="showLogin()" id="login-tab" class="pb-3 px-6 font-medium text-sm transition-all tab-active">
                    @lang('labels.sign_in')
                </button>
                <button type="button" onclick="showRegister()" id="register-tab" class="pb-3 px-6 font-medium text-sm text-gray-400 hover:text-gray-600 transition-all">
                    @lang('labels.register')
                </button>
            </div>
            
            <div id="login-form-container">
                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form">
                    @csrf
                    
                    @if(isset($selectedSchool) && $selectedSchool)
                        <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">@lang('labels.email')</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl input-focus focus:outline-none transition-all @error('email') border-red-500 bg-red-50 @enderror"
                                placeholder="you@example.com">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1 text-xs"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">@lang('labels.password')</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl input-focus focus:outline-none transition-all @error('password') border-red-500 bg-red-50 @enderror"
                                placeholder="@lang('labels.password')">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <i id="eye-icon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1 text-xs"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="w-5 h-5 rounded border-gray-300 focus:ring-2" style="--tw-ring-color: var(--primary)">
                            <span class="ml-2 text-sm text-gray-600">@lang('labels.remember_me')</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm font-medium transition-colors" style="color: var(--primary)" onmouseover="this.style.color='color-mix(in srgb, var(--primary) 80%, black)'" onmouseout="this.style.color='var(--primary)'">
                            @lang('labels.forgot_password')
                        </a>
                    </div>
                    
                    <button type="submit" class="w-full btn-primary text-white py-3.5 sm:py-3 rounded-xl font-semibold text-sm tracking-wide flex items-center justify-center shadow-lg" id="login-btn">
                        <span id="login-text">@lang('labels.sign_in')</span>
                        <svg id="login-spinner" class="hidden animate-spin ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
            
            <div id="register-form-container" class="hidden">
                <form method="POST" action="{{ route('register') }}" class="space-y-5" id="register-form">
                    @csrf
                    
                    @if(isset($selectedSchool) && $selectedSchool)
                        <input type="hidden" name="school_id" value="{{ $selectedSchool->id }}">
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">@lang('labels.full_name')</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl input-focus focus:outline-none transition-all @error('name') border-red-500 bg-red-50 @enderror"
                                placeholder="@lang('labels.full_name_placeholder')">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1 text-xs"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">@lang('labels.email')</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl input-focus focus:outline-none transition-all @error('email') border-red-500 bg-red-50 @enderror"
                                placeholder="you@example.com">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1 text-xs"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">@lang('labels.password')</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input type="password" name="password" id="reg-password" required
                                class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl input-focus focus:outline-none transition-all @error('password') border-red-500 bg-red-50 @enderror"
                                placeholder="@lang('labels.password_min')">
                            <button type="button" onclick="toggleRegPassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <i id="reg-eye-icon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1.5 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1 text-xs"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">@lang('labels.confirm_password')</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="reg-confirm-password" required
                                class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl input-focus focus:outline-none transition-all"
                                placeholder="@lang('labels.confirm_password')">
                            <button type="button" onclick="toggleRegConfirmPassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                <i id="reg-confirm-eye-icon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full btn-primary text-white py-3.5 sm:py-3 rounded-xl font-semibold text-sm tracking-wide flex items-center justify-center shadow-lg" id="register-btn">
                        <span id="register-text">@lang('labels.register')</span>
                        <svg id="register-spinner" class="hidden animate-spin ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <div class="flex items-center justify-center space-x-4 mb-4">
                    <a href="{{ route('lang', 'en') }}" class="text-sm transition-colors {{ app()->getLocale() == 'en' ? 'font-semibold' : 'text-gray-400' }}" style="{{ app()->getLocale() == 'en' ? 'color: var(--primary)' : '' }}">
                        English
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('lang', 'sw') }}" class="text-sm transition-colors {{ app()->getLocale() == 'sw' ? 'font-semibold' : 'text-gray-400' }}" style="{{ app()->getLocale() == 'sw' ? 'color: var(--primary)' : '' }}">
                        Kiswahili
                    </a>
                </div>
                <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 hover:text-gray-600 text-sm transition-colors">
                    <i class="fas fa-arrow-left mr-2 text-xs"></i>@lang('labels.back_home')
                </a>
            </div>
        </div>
        
        @if(app_footer())
            <p class="text-center text-gray-400 text-xs mt-6">
                {!! app_footer() !!}
            </p>
        @endif
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
        
        document.getElementById('login-form').addEventListener('submit', function() {
            var btn = document.getElementById('login-btn');
            btn.disabled = true;
            document.getElementById('login-spinner').classList.remove('hidden');
        });
        
        document.getElementById('register-form').addEventListener('submit', function() {
            var btn = document.getElementById('register-btn');
            btn.disabled = true;
            document.getElementById('register-spinner').classList.remove('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
