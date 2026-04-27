@extends('layouts.super-admin')

@section('page-title', 'Edit Plan')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Plan</h1>
            <p class="text-gray-600">Update plan details</p>
        </div>
        <a href="{{ route('super-admin.subscriptions.plans') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Plans
        </a>
    </div>

    <form action="{{ route('super-admin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $plan->slug) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required pattern="[a-z0-9-]+">
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $plan->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Pricing</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $plan->price) }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            @error('price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                            <input type="text" name="currency" id="currency" value="{{ old('currency', $plan->currency) }}" maxlength="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="billing_period" class="block text-sm font-medium text-gray-700 mb-2">Billing Period</label>
                        <select name="billing_period" id="billing_period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="monthly" {{ old('billing_period', $plan->billing_period) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('billing_period', $plan->billing_period) === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="yearly" {{ old('billing_period', $plan->billing_period) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                        @error('billing_period')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                        <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        @error('duration_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Limits</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="max_students" class="block text-sm font-medium text-gray-700 mb-2">Max Students (0 = Unlimited)</label>
                        <input type="number" name="max_students" id="max_students" value="{{ old('max_students', $plan->max_students ?? 0) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="max_staff" class="block text-sm font-medium text-gray-700 mb-2">Max Staff (0 = Unlimited)</label>
                        <input type="number" name="max_staff" id="max_staff" value="{{ old('max_staff', $plan->max_staff ?? 0) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="max_programs" class="block text-sm font-medium text-gray-700 mb-2">Max Programs (0 = Unlimited)</label>
                        <input type="number" name="max_programs" id="max_programs" value="{{ old('max_programs', $plan->max_programs ?? 0) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="max_applications" class="block text-sm font-medium text-gray-700 mb-2">Max Applications (0 = Unlimited)</label>
                        <input type="number" name="max_applications" id="max_applications" value="{{ old('max_applications', $plan->max_applications ?? 0) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Features</h2>
                
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_document_upload" value="1" {{ $plan->allow_document_upload ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Document Upload</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="allow_payment_gateway" value="1" {{ $plan->allow_payment_gateway ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Payment Gateway</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="allow_custom_branding" value="1" {{ $plan->allow_custom_branding ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Custom Branding</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="allow_api_access" value="1" {{ $plan->allow_api_access ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">API Access</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="allow_priority_support" value="1" {{ $plan->allow_priority_support ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Priority Support</span>
                    </label>

                    <label class="flex items-center mt-4 pt-4 border-t">
                        <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('super-admin.subscriptions.plans') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i> Update Plan
            </button>
        </div>
    </form>
</div>
@endsection
