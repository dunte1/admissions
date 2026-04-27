@extends('layouts.admin')

@section('title', 'Subscription Expired')

@section('header', 'Subscription Expired')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Subscription Expired</h2>
            <p class="mt-2 text-gray-600">Your subscription has expired. Please renew to continue using the platform.</p>
            
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose a Plan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                    @foreach($plans as $plan)
                    <div class="border border-gray-200 rounded-lg p-6 hover:border-purple-500 transition-colors">
                        <h4 class="text-lg font-semibold text-gray-900">{{ $plan->name }}</h4>
                        <p class="mt-2 text-3xl font-bold text-gray-900">
                            ${{ number_format($plan->price, 0) }}
                            <span class="text-sm font-normal text-gray-500">/{{ $plan->billing_period }}</span>
                        </p>
                        <p class="mt-2 text-sm text-gray-500">{{ $plan->description }}</p>
                        
                        <ul class="mt-4 space-y-2">
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
                                {{ $plan->max_programs ?? 'Unlimited' }} Programs
                            </li>
                        </ul>

                        <form action="{{ route('admin.subscription.subscribe', $plan) }}" method="POST" class="mt-6">
                            @csrf
                            <select name="billing_period" class="w-full mb-3 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly (Save 10%)</option>
                                <option value="yearly">Yearly (Save 25%)</option>
                            </select>
                            <button type="submit" class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                Subscribe
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Need help choosing? Contact our sales team at 
                    <a href="mailto:sales@admissionportal.com" class="text-purple-600 hover:text-purple-500">sales@admissionportal.com</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
