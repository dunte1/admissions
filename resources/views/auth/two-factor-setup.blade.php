<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Two-Factor Authentication</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
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
                <h1 class="text-2xl font-bold text-gray-900">Setup Two-Factor Authentication</h1>
                <p class="text-gray-500 mt-2">Scan this QR code with your authenticator app</p>
            </div>

            @if(session('success') && session('recovery_codes'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="font-semibold text-green-800 mb-2">Save your recovery codes!</p>
                    <p class="text-sm text-green-700 mb-3">Store these codes in a safe place. You can use them to access your account if you lose your phone.</p>
                    <div class="grid grid-cols-2 gap-2 bg-white p-3 rounded border border-green-200">
                        @foreach(session('recovery_codes') as $code)
                            <span class="font-mono text-sm text-green-800">{{ $code }}</span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('home') }}" class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-purple-700">
                    Continue to Dashboard
                </a>
            @else
                <div class="text-center mb-6">
                    <canvas id="qr-code" class="mx-auto"></canvas>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Manual entry key:</p>
                    <p class="font-mono text-xs text-gray-600 break-all">{{ $secret }}</p>
                </div>

                <form action="{{ route('two-factor.verify') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Enter verification code</label>
                        <input type="text" name="code" maxlength="6" placeholder="000000" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-2xl tracking-widest font-mono focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            autocomplete="one-time-code">
                        @error('code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700">
                        Verify & Enable
                    </button>
                </form>

                <form action="{{ route('two-factor.cancel') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full text-gray-500 py-2 text-sm hover:text-gray-700">
                        Cancel
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(!session('recovery_codes'))
    <script>
        QRCode.toCanvas(document.getElementById('qr-code'), '{!! $qrCodeUrl !!}', {
            width: 200,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        });
    </script>
    @endif
</body>
</html>
