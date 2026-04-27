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
</style>

<div class="min-h-screen flex">
    <div class="hidden lg:flex lg:flex-1 brand-gradient items-center justify-center p-12">
        <div class="max-w-lg text-center text-white">
            <h3 class="text-3xl font-bold mb-4">Join {{ $school->name }}</h3>
            <p class="text-white/80 text-lg">Create your student account and start your application today.</p>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                @if($school->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-20 mx-auto mb-4 object-contain">
                @else
                    <div class="h-20 w-20 mx-auto brand-gradient rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                @endif
                <h2 class="text-3xl font-bold" style="color: var(--primary-color)">
                    {{ $school->name }}
                </h2>
                <p class="mt-2 text-sm text-gray-600">Create your account</p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('school.register') }}">
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

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input id="first_name" name="first_name" type="text" required
                                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                                style="--tw-ring-color: var(--primary-color)"
                                value="{{ old('first_name') }}">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input id="last_name" name="last_name" type="text" required
                                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                                style="--tw-ring-color: var(--primary-color)"
                                value="{{ old('last_name') }}">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <input id="email" name="email" type="email" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color)"
                            value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number (Optional)</label>
                        <input id="phone" name="phone" type="tel"
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color)"
                            value="{{ old('phone') }}">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color)">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                            style="--tw-ring-color: var(--primary-color)">
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full py-3 px-4 border border-transparent rounded-lg font-medium text-white shadow-lg hover:shadow-xl transition-all">
                    Create Account
                </button>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('school.login') }}" class="font-medium hover:underline" style="color: var(--primary-color)">
                            Sign in
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection