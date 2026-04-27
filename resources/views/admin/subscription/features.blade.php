@extends('layouts.admin')

@section('title', 'Plans & Features')

@section('header', 'Plans & Features')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900">Choose Your Plan</h1>
        <p class="mt-4 text-lg text-gray-600">Select the perfect plan for your institution's needs</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden {{ $plan->slug === 'professional' ? 'ring-2 ring-purple-600' : '' }}">
            @if($plan->slug === 'professional')
            <div class="bg-purple-600 text-white text-center text-sm font-medium py-1">
                Most Popular
            </div>
            @endif
            
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ $plan->description }}</p>
                
                <div class="mt-6">
                    <span class="text-4xl font-bold text-gray-900">${{ number_format($plan->price, 0) }}</span>
                    <span class="text-gray-500">/{{ $plan->billing_period }}</span>
                </div>

                <ul class="mt-6 space-y-3">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">
                            <strong>{{ $plan->max_students ?? 'Unlimited' }}</strong> Students
                        </span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">
                            <strong>{{ $plan->max_staff ?? 'Unlimited' }}</strong> Staff Members
                        </span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">
                            <strong>{{ $plan->max_programs ?? 'Unlimited' }}</strong> Programs
                        </span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">Application Management</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 {{ $plan->allow_document_upload ? 'text-green-500' : 'text-gray-300' }} mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">Document Upload</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 {{ $plan->allow_payment_gateway ? 'text-green-500' : 'text-gray-300' }} mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">M-PESA & PayPal</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 {{ $plan->allow_custom_branding ? 'text-green-500' : 'text-gray-300' }} mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">Custom Branding</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 {{ $plan->allow_api_access ? 'text-green-500' : 'text-gray-300' }} mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">API Access</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 {{ $plan->allow_priority_support ? 'text-green-500' : 'text-gray-300' }} mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm text-gray-600">Priority Support</span>
                    </li>
                </ul>

                <form action="{{ route('admin.subscription.subscribe', $plan) }}" method="POST" class="mt-6">
                    @csrf
                    <select name="billing_period" class="w-full mb-3 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly (Save 10%)</option>
                        <option value="yearly">Yearly (Save 25%)</option>
                    </select>
                    <button type="submit" class="w-full py-2 px-4 rounded-md text-sm font-medium {{ $plan->slug === 'professional' ? 'bg-purple-600 text-white hover:bg-purple-700' : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }}">
                        Get Started
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12 text-center">
        <p class="text-gray-600">
            All plans include a 14-day free trial. No credit card required.
        </p>
        <p class="mt-2 text-sm text-gray-500">
            Questions? <a href="mailto:support@admissionportal.com" class="text-purple-600 hover:text-purple-500">Contact our sales team</a>
        </p>
    </div>
</div>
@endsection
