<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name', 'Admission Portal') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            height: 100vh;
        }
        .login-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
        }
        .card-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        }
        .input-focus {
            transition: all 0.2s ease;
        }
        .input-focus:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.25);
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    @php
        $systemLogo = \DB::table('settings')->where('key', 'system_logo')->whereNull('school_id')->value('value');
        $systemName = \DB::table('settings')->where('key', 'system_name')->whereNull('school_id')->value('value');
        if (!$systemName) {
            $systemName = \DB::table('settings')->where('key', 'app_name')->whereNull('school_id')->value('value');
        }
        if (!$systemName) {
            $systemName = config('app.name', 'Admission Portal');
        }
    @endphp
    
    <div class="w-full max-w-sm flex flex-col justify-center h-full -mt-8">
        <div class="bg-white rounded-2xl card-shadow p-7">
            <div class="text-center mb-6">
                @if($systemLogo)
                    <img src="{{ asset('storage/' . $systemLogo) }}" alt="{{ $systemName }}" class="h-14 w-auto mx-auto mb-3">
                @else
                    <div class="w-14 h-14 mx-auto bg-gradient-to-br from-sky-500 to-blue-600 rounded-xl flex items-center justify-center mb-3 shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                @endif
                
                <h1 class="text-xl font-bold text-gray-900">{{ $systemName }}</h1>
            </div>

            <form method="POST" action="{{ route('super-admin.login') }}" class="space-y-4">
                @csrf
                
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        autocomplete="email"
                        class="input-focus w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none text-sm"
                        placeholder="Email address"
                        value="{{ old('email') }}"
                    >
                </div>

                <div>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        class="input-focus w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none text-sm"
                        placeholder="Password"
                    >
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="remember" class="ml-2 text-xs text-gray-600">Remember</label>
                    </div>
                    <a href="{{ route('super-admin.password.request') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Forgot?</a>
                </div>

                <button type="submit" class="btn-primary w-full py-2.5 px-4 rounded-lg font-semibold text-white text-sm shadow-lg">
                    Sign In
                </button>
            </form>

            <div class="mt-5 pt-4 border-t border-gray-100">
                <p class="text-center text-xs text-gray-500">Authorized access only</p>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('home') }}" class="text-xs text-gray-500 hover:text-gray-700">← Back to website</a>
        </div>

        <div class="mt-2 text-center">
            <span class="text-xs text-gray-400">© {{ date('Y') }} {{ $systemName }}</span>
        </div>
    </div>
</body>
</html>