<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Error') - {{ config('app.name', 'Admission Portal') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-8 text-center">
                <div class="text-6xl font-bold text-white mb-2">@yield('code', 'Error')</div>
                <h1 class="text-2xl font-semibold text-white">@yield('title', 'Something went wrong')</h1>
            </div>
            
            <div class="p-6 text-center">
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600">@yield('message', 'An unexpected error occurred. Please try again later.')</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Home
                    </a>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 text-center text-sm text-gray-500">
                <p>Need help? Contact <a href="mailto:support@{{ request()->getHost() }}" class="text-indigo-600 hover:underline">support</a></p>
            </div>
        </div>
    </div>
</body>
</html>
