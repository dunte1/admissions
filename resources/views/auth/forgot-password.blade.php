<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ app_name() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .brand-gradient {
            background: linear-gradient(135deg, #00008B 0%, #000066 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
        }
        .navy-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 139, 0.25);
        }
        .input-focus:focus {
            border-color: #00008B;
            box-shadow: 0 0 0 3px rgba(0, 0, 139, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #00008B 0%, #000066 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #000066 0%, #00004d 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 139, 0.3);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
    </style>
</head>
<body class="min-h-screen flex">
    <div class="hidden lg:flex lg:w-1/2 brand-gradient relative overflow-hidden min-h-screen">
        <div class="absolute inset-0">
            <div class="absolute top-20 -left-48 w-96 h-96 bg-white/10 rounded-full"></div>
            <div class="absolute bottom-20 right-10 w-64 h-64 bg-white/10 rounded-full"></div>
            <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-white/10 rounded-full"></div>
        </div>
        
        <div class="relative z-10 flex flex-col justify-center items-center w-full p-12 text-white">
            <div class="text-center max-w-lg">
                <div class="mb-8 floating">
                    <div class="w-32 h-32 mx-auto bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-2xl">
                        <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-5xl font-bold mb-4 tracking-tight">{{ app_name() }}</h1>
                <p class="text-xl text-white/80">{{ system_setting('tagline', 'Streamline Admissions') }}</p>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gradient-to-b from-gray-50 to-gray-100">
        <div class="w-full max-w-md">
            <div class="lg:hidden text-center mb-8">
                <div class="w-20 h-20 mx-auto brand-gradient rounded-2xl flex items-center justify-center shadow-lg mb-4">
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-[#00008B]">{{ app_name() }}</h1>
                <p class="text-gray-500 text-sm">{{ system_setting('tagline', 'Streamline Admissions') }}</p>
            </div>

            <div class="bg-white rounded-3xl navy-shadow p-8 sm:p-10">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 mx-auto bg-[#00008B]/10 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-key text-2xl text-[#00008B]"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h2>
                    <p class="text-gray-500">Enter your email to receive a reset link</p>
                </div>

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

                <form method="POST" action="{{ route('password.request') }}" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
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

                    <button type="submit" class="w-full btn-primary text-white py-3.5 rounded-xl font-semibold text-sm tracking-wide shadow-lg">
                        SEND RESET LINK
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center text-gray-500 hover:text-[#00008B] text-sm transition-colors">
                        <i class="fas fa-arrow-left mr-2 text-xs"></i>Back to Sign In
                    </a>
                </div>
            </div>

            <p class="text-center text-gray-400 text-xs mt-6">
                &copy; {{ date('Y') }} {{ app_name() }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
