@extends('layouts.admin')

@section('page-title', 'Active Sessions')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Active Sessions</h1>
        <p class="text-gray-600">Manage your active login sessions</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Current Sessions</h2>
                    <p class="text-sm text-gray-500">These are devices that are currently logged into your account.</p>
                </div>
                <button onclick="document.getElementById('logoutAllModal').classList.remove('hidden')" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout All Devices
                </button>
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(count($sessions) === 0)
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-laptop text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500">No active sessions found.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($sessions as $session)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-laptop text-blue-600"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $session->ip_address ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">
                                    Last active: {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('sessions.logout-other') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="session_id" value="{{ $session->id }}">
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<div id="logoutAllModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <form action="{{ route('sessions.logout-all') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Logout from All Devices</h3>
                            <p class="mt-2 text-sm text-gray-500">This will log you out from all devices except this one. Please enter your password to confirm.</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Logout All</button>
                    <button type="button" onclick="document.getElementById('logoutAllModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
