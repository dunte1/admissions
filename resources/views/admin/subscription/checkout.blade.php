@extends('layouts.admin')

@section('page-title', 'Checkout - ' . ($plan->name ?? 'Subscription'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.subscription.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Subscription
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 text-center mb-8">Complete Your Order</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between py-3 border-b border-gray-100">
                            <div>
                                <p class="font-medium text-gray-900">{{ $plan->name }} Plan</p>
                                <p class="text-sm text-gray-500">{{ ucfirst($request->billing_period ?? 'monthly') }} billing</p>
                            </div>
                            <p class="font-medium text-gray-900">${{ number_format($price ?? $plan->price, 2) }}</p>
                        </div>
                        <div class="flex justify-between py-3 border-b border-gray-100">
                            <p class="text-gray-600">Subtotal</p>
                            <p class="font-medium text-gray-900">${{ number_format($price ?? $plan->price, 2) }}</p>
                        </div>
                        <div class="flex justify-between py-3">
                            <p class="text-lg font-semibold text-gray-900">Total</p>
                            <p class="text-lg font-bold text-blue-600">${{ number_format($price ?? $plan->price, 2) }}</p>
                        </div>
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Payment Method</h3>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="mpesa" checked class="text-blue-600">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">M-PESA</p>
                                    <p class="text-sm text-gray-500">Pay with M-PESA mobile money</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="paypal" class="text-blue-600">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">PayPal</p>
                                    <p class="text-sm text-gray-500">Pay with PayPal account</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="manual" class="text-blue-600">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">Bank Transfer</p>
                                    <p class="text-sm text-gray-500">Manual payment via bank transfer</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Details</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Plan</dt>
                            <dd class="font-medium text-gray-900">{{ $plan->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Billing</dt>
                            <dd class="font-medium text-gray-900">{{ ucfirst($request->billing_period ?? 'monthly') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Duration</dt>
                            <dd class="font-medium text-gray-900">{{ $durationDays ?? $plan->duration_days }} days</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">School</dt>
                            <dd class="font-medium text-gray-900">{{ $school->name }}</dd>
                        </div>
                    </dl>

                    <form action="{{ route('admin.subscription.subscribe', $plan) }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="billing_period" value="{{ $request->billing_period ?? 'monthly' }}">
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-lock mr-2"></i> Complete Order
                        </button>
                    </form>

                    <p class="mt-4 text-xs text-gray-500 text-center">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Your payment is secure and encrypted
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
