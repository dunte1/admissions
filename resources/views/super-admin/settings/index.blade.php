@extends('layouts.super-admin')

@section('header', 'Global Settings')
@section('breadcrumb', 'System-wide configuration')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">System Settings</h3>
                    <p class="text-xs text-gray-500 mt-1">Global configuration</p>
                </div>
                <nav class="divide-y divide-gray-100">
                    <a href="{{ route('super-admin.settings', ['tab' => 'system']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'system' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                        </svg>
                        System
                    </a>
                    <a href="{{ route('super-admin.settings', ['tab' => 'general']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'general' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        General
                    </a>
                    <a href="{{ route('super-admin.settings', ['tab' => 'features']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'features' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        System Features
                    </a>
                    <a href="{{ route('super-admin.settings', ['tab' => 'mail']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'mail' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Mail
                    </a>
                    <a href="{{ route('super-admin.settings', ['tab' => 'payment']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'payment' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Payment
                    </a>
                    <a href="{{ route('super-admin.settings', ['tab' => 'sms']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'sms' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        SMS
                    </a>
                    <a href="{{ route('super-admin.settings', ['tab' => 'security']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'security' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Security
                    </a>
                    <a href="{{ route('super-admin.settings', ['tab' => 'branding']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'branding' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        Branding
                    </a>
                    <a href="{{ route('super-admin.settings', ['tab' => 'seo']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $activeTab === 'seo' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        SEO
                    </a>
                    <form action="{{ route('super-admin.settings.clear-cache') }}" method="POST" class="p-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Clear Cache
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <div class="lg:col-span-3">
            @if($activeTab === 'system')
                @include('super-admin.settings.partials.system')
            @elseif($activeTab === 'general')
                @include('super-admin.settings.partials.general')
            @elseif($activeTab === 'features')
                @include('super-admin.settings.partials.features')
            @elseif($activeTab === 'mail')
                @include('super-admin.settings.partials.mail')
            @elseif($activeTab === 'payment')
                @include('super-admin.settings.partials.payment')
            @elseif($activeTab === 'sms')
                @include('super-admin.settings.partials.sms')
            @elseif($activeTab === 'security')
                @include('super-admin.settings.partials.security')
            @elseif($activeTab === 'branding')
                @include('super-admin.settings.partials.branding')
            @elseif($activeTab === 'seo')
                @include('super-admin.settings.partials.seo')
            @endif
        </div>
    </div>
</div>
@endsection