@extends('layouts.admin')

@section('page-title', 'Choose Plan - ' . ($plan->name ?? 'Subscription'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.subscription.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Subscription
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Choose Your Plan</h1>
            <p class="mt-2 text-gray-600">Select a billing period for the {{ $plan->name }} plan</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200 mb-8">
            <div class="p-8 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-center">
                <h2 class="text-2xl font-bold">{{ $plan->name }} Plan</h2>
                <p class="mt-2 text-blue-100">{{ $plan->description }}</p>
            </div>

            <div class="p-8">
                <form action="{{ route('admin.subscription.subscribe', $plan) }}" method="POST">
                    @csrf
                    <input type="hidden" name="billing_period" id="billing_period" value="monthly">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <label class="relative">
                            <input type="radio" name="billing_option" value="monthly" class="peer sr-only" checked>
                            <div class="p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-900">Monthly</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900">${{ $plan->price }}</p>
                                    <p class="text-sm text-gray-500">per month</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative">
                            <input type="radio" name="billing_option" value="quarterly" class="peer sr-only">
                            <div class="p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                <div class="text-center">
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded mb-2">Save 10%</span>
                                    <p class="text-sm font-medium text-gray-900">Quarterly</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900">${{ number_format($plan->price * 2.7, 2) }}</p>
                                    <p class="text-sm text-gray-500">per quarter</p>
                                </div>
                            </div>
                        </label>

                        <label class="relative">
                            <input type="radio" name="billing_option" value="yearly" class="peer sr-only">
                            <div class="p-6 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                <div class="text-center">
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded mb-2">Save 25%</span>
                                    <p class="text-sm font-medium text-gray-900">Yearly</p>
                                    <p class="mt-2 text-2xl font-bold text-gray-900">${{ number_format($plan->price * 9, 2) }}</p>
                                    <p class="text-sm text-gray-500">per year</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.subscription.index') }}" class="text-gray-600 hover:text-gray-800">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Continue to Checkout
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Plan Features</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $plan->max_students ?? '∞' }}</p>
                    <p class="text-sm text-gray-500">Students</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $plan->max_staff ?? '∞' }}</p>
                    <p class="text-sm text-gray-500">Staff</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $plan->max_programs ?? '∞' }}</p>
                    <p class="text-sm text-gray-500">Programs</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $plan->max_applications ?? '∞' }}</p>
                    <p class="text-sm text-gray-500">Applications</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('input[name="billing_option"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('billing_period').value = this.value;
        });
    });
</script>
@endpush
@endsection
