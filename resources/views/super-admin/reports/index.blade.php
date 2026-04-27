@extends('layouts.super-admin')

@section('title', 'Reports & Analytics')

@section('header', 'Reports & Analytics')

@section('breadcrumb', 'System Reports')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
                <p class="text-sm text-gray-500 mt-1">Access comprehensive reports and insights</p>
            </div>
        </div>

        {{-- Report Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Analytics --}}
            <a href="{{ route('super-admin.reports.analytics') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-purple-200 transition-all duration-300">
                <div class="p-6">
                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Analytics Dashboard</h3>
                    <p class="text-sm text-gray-500">Overview & trends across all schools</p>
                    <div class="mt-4 flex items-center text-purple-600 text-sm font-medium">
                        <span>View Analytics</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Applications --}}
            <a href="{{ route('super-admin.reports.applications') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-green-200 transition-all duration-300">
                <div class="p-6">
                    <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Applications Report</h3>
                    <p class="text-sm text-gray-500">Track all applications across institutions</p>
                    <div class="mt-4 flex items-center text-green-600 text-sm font-medium">
                        <span>View Report</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Payments --}}
            <a href="{{ route('super-admin.reports.payments') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-yellow-200 transition-all duration-300">
                <div class="p-6">
                    <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Payments Report</h3>
                    <p class="text-sm text-gray-500">Revenue & transaction details</p>
                    <div class="mt-4 flex items-center text-yellow-600 text-sm font-medium">
                        <span>View Report</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Schools --}}
            <a href="{{ route('super-admin.reports.schools') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-blue-200 transition-all duration-300">
                <div class="p-6">
                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Schools Report</h3>
                    <p class="text-sm text-gray-500">Institution performance metrics</p>
                    <div class="mt-4 flex items-center text-blue-600 text-sm font-medium">
                        <span>View Report</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Programs --}}
            <a href="{{ route('super-admin.reports.programs') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-indigo-200 transition-all duration-300">
                <div class="p-6">
                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Programs Report</h3>
                    <p class="text-sm text-gray-500">Programs analytics & popularity</p>
                    <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                        <span>View Report</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Users --}}
            <a href="{{ route('super-admin.reports.users') }}" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-pink-200 transition-all duration-300">
                <div class="p-6">
                    <div class="w-14 h-14 bg-pink-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Users Report</h3>
                    <p class="text-sm text-gray-500">User management & statistics</p>
                    <div class="mt-4 flex items-center text-pink-600 text-sm font-medium">
                        <span>View Report</span>
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        {{-- Quick Stats --}}
        <div class="mt-8 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-6 text-white">
            <h3 class="text-lg font-semibold mb-4">Quick Overview</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-white/70 text-sm">Total Schools</p>
                    <p class="text-2xl font-bold">{{ \App\Models\School::count() }}</p>
                </div>
                <div>
                    <p class="text-white/70 text-sm">Total Applications</p>
                    <p class="text-2xl font-bold">{{ number_format(\App\Models\Application::withoutGlobalScopes()->count()) }}</p>
                </div>
                <div>
                    <p class="text-white/70 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold">{{ number_format(\App\Models\Payment::withoutGlobalScopes()->where('status', 'completed')->sum('amount'), 0) }}</p>
                </div>
                <div>
                    <p class="text-white/70 text-sm">Total Users</p>
                    <p class="text-2xl font-bold">{{ \App\Models\User::withoutGlobalScopes()->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
