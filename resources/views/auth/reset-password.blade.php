<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ app_name() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        :root {
            --primary: {{ primary_color() }};
            --secondary: {{ system_setting('secondary_color', '#10B981') }};
        }
        .brand-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
    </style>
</head>
<body class="min-h-screen flex">
    <div class="flex flex-col lg:flex-row w-full">
        <div class="hidden lg:flex lg:w-1/2 brand-gradient relative overflow-hidden min-h-screen">
            <div class="relative z-10 flex flex-col justify-center items-center w-full p-12 text-white">
                <div class="text-center max-w-lg">
                    <div class="mb-8">
                        @if(system_setting('system_logo'))
                            <img src="{{ asset('storage/' . system_setting('system_logo')) }}" alt="{{ app_name() }}" class="w-32 h-32 mx-auto object-contain rounded-3xl bg-white/20 p-4 shadow-2xl">
                        @else
                            <div class="w-32 h-32 mx-auto bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-2xl">
                                <i class="fas fa-graduation-cap text-6xl"></i>
                            </div>
                        @endif
                    </div>
                    <h1 class="text-4xl font-bold mb-4">{{ app_name() }}</h1>
                    <p class="text-xl text-white/90">Admission Portal</p>
                </div>
            </div>
        </div>

        <div class="flex-1 flex items-center justify-center p-8 bg-gray-50">
            <div class="w-full max-w-md">
                <div class="text-center lg:text-left mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Reset Password</h2>
                    <p class="mt-2 text-gray-600">Enter your new password below</p>
                </div>

                @if(session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-6 bg-white p-8 rounded-2xl shadow-lg">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', request()->email) }}" required autofocus
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <i class="fas fa-key mr-2 mt-1"></i>
                        Reset Password
                    </button>

                    <p class="text-center text-sm text-gray-600">
                        Remember your password? 
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
