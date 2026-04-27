@extends('layouts.admin')

@section('title', 'Subscription Management')

@section('header', 'Subscription')

@section('content')
<div class="space-y-6">
    @if(session('warning'))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Current Subscription --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Current Subscription</h2>
                @if($subscription && $subscription->daysRemaining() <= 7)
                <span class="px-3 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">Expires in {{ $subscription->daysRemaining() }} days</span>
                @endif
            </div>

            @if($subscription)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Plan</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $plan->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subscription->statusBadgeClass }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Expires</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $subscription->expires_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Days Remaining</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $subscription->daysRemaining() }}</p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Plan Features</h3>
                <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $plan->max_students ?? 'Unlimited' }} Students
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $plan->max_staff ?? 'Unlimited' }} Staff
                    </li>
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $plan->max_programs ?? 'Unlimited' }} Programs
                    </li>
                    @if($plan->allow_document_upload)
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Document Upload
                    </li>
                    @endif
                    @if($plan->allow_payment_gateway)
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Payment Gateway
                    </li>
                    @endif
                    @if($plan->allow_custom_branding)
                    <li class="flex items-center text-sm text-gray-600">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Custom Branding
                    </li>
                    @endif
                </ul>
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('admin.subscription.features') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">
                    Compare Plans
                </a>
                @if($subscription->daysRemaining() <= 7)
                <form action="{{ route('admin.subscription.subscribe', $plan) }}" method="POST">
                    @csrf
                    <input type="hidden" name="billing_period" value="yearly">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700">
                        Renew Now
                    </button>
                </form>
                @endif
            </div>
            @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Active Subscription</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by choosing a plan that fits your needs.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.subscription.features') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">
                        View Plans
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Usage Stats --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Current Usage</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Students</span>
                        <span class="text-sm text-gray-500">
                            {{ $school->users()->whereHas('roles', fn($q) => $q->where('name', 'student'))->count() }}
                            @if($plan?->max_students)/ {{ $plan->max_students }}@endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php $studentPercent = $plan?->max_students ? min(100, ($school->users()->whereHas('roles', fn($q) => $q->where('name', 'student'))->count() / $plan->max_students) * 100) : 0; @endphp
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $studentPercent }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Staff</span>
                        <span class="text-sm text-gray-500">
                            {{ $school->users()->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'registrar', 'accountant', 'reviewer', 'support']))->count() }}
                            @if($plan?->max_staff)/ {{ $plan->max_staff }}@endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php $staffPercent = $plan?->max_staff ? min(100, ($school->users()->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'registrar', 'accountant', 'reviewer', 'support']))->count() / $plan->max_staff) * 100) : 0; @endphp
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $staffPercent }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Programs</span>
                        <span class="text-sm text-gray-500">
                            {{ $school->programs()->count() }}
                            @if($plan?->max_programs)/ {{ $plan->max_programs }}@endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php $programPercent = $plan?->max_programs ? min(100, ($school->programs()->count() / $plan->max_programs) * 100) : 0; @endphp
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $programPercent }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
