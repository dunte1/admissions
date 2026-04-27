@extends('layouts.guest')

@section('content')
<style>
    :root {
        --primary-color: {{ $branding['primary_color'] ?? '#7C3AED' }};
        --secondary-color: {{ $branding['secondary_color'] ?? '#10B981' }};
    }
    .brand-gradient {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color)cc 50%, var(--primary-color)99 100%);
    }
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color)cc 100%);
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-color)cc 0%, var(--primary-color)99 100%);
    }
    .focus-ring:focus {
        --tw-ring-color: var(--primary-color);
    }
</style>

<div class="min-h-screen flex">
    <div class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-20 mx-auto mb-4 object-contain">
                @else
                    <div class="h-20 w-20 mx-auto brand-gradient rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                @endif
                <h2 class="text-3xl font-bold" style="color: var(--primary-color)">
                    {{ $school->name }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('school.login') }}">
                @csrf
                
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('info'))
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('info') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent focus-ring @error('email') border-red-300 @enderror"
                            value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent focus-ring @error('password') border-red-300 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            style="color: var(--primary-color)">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('school.password.request') }}" class="font-medium hover:underline" style="color: var(--primary-color)">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full py-3 px-4 border border-transparent rounded-lg font-medium text-white shadow-lg hover:shadow-xl transition-all">
                    Sign in
                </button>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('school.register') }}" class="font-medium hover:underline" style="color: var(--primary-color)">
                            Create one
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <div class="hidden lg:flex lg:flex-1 brand-gradient items-center justify-center p-12">
        <div class="max-w-lg text-center text-white">
            <h3 class="text-3xl font-bold mb-4">Welcome Back</h3>
            <p class="text-white/80 text-lg">Access your application dashboard and track your admission status.</p>
            
            @if($school->admissions_contact_email)
            <div class="mt-8 pt-8 border-t border-white/20">
                <p class="text-sm text-white/60">Need help?</p>
                <p class="text-white font-medium">{{ $school->admissions_contact_email }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection