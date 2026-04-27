<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Two-Factor Authentication</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Two-Factor Authentication</h1>
                <p class="text-gray-500 mt-2">Enter the code from your authenticator app</p>
            </div>

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @error('code')
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                </div>
            @enderror

            @error('recovery_code')
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm">{{ $message }}</p>
                </div>
            @enderror

            <form action="{{ route('two-factor.verify.submit') }}" method="POST" class="mb-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Authentication Code</label>
                    <input type="text" name="code" maxlength="6" placeholder="000000" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-2xl tracking-widest font-mono focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        autocomplete="one-time-code">
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700">
                    Verify
                </button>
            </form>

            <div class="relative mb-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or use a recovery code</span>
                </div>
            </div>

            <form action="{{ route('two-factor.recovery') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recovery Code</label>
                    <input type="text" name="recovery_code" placeholder="XXXX-XXXX" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center font-mono focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        autocomplete="off">
                </div>
                <button type="submit" class="w-full bg-gray-100 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-200">
                    Use Recovery Code
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-gray-500 py-2 text-sm hover:text-gray-700">
                    Sign out and use different account
                </button>
            </form>
        </div>
    </div>
</body>
</html>
