@extends('layouts.super-admin')

@section('title', 'Create Plan')

@section('header', 'Create Plan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('super-admin.plans.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Plan Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" required pattern="[a-z0-9-]+"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                <p class="mt-1 text-sm text-gray-500">URL-friendly identifier (lowercase, no spaces)</p>
                @error('slug')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" required min="0" step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Currency</label>
                    <select name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                        <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="KES" {{ old('currency') === 'KES' ? 'selected' : '' }}>KES</option>
                        <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Billing Period</label>
                    <select name="billing_period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                        <option value="monthly" {{ old('billing_period') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('billing_period') === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="yearly" {{ old('billing_period') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Duration (days)</label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', 30) }}" required min="1"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Students</label>
                    <input type="number" name="max_students" value="{{ old('max_students') }}" min="0" placeholder="0 = unlimited"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Staff</label>
                    <input type="number" name="max_staff" value="{{ old('max_staff') }}" min="0" placeholder="0 = unlimited"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Max Programs</label>
                    <input type="number" name="max_programs" value="{{ old('max_programs') }}" min="0" placeholder="0 = unlimited"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Features</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_document_upload" value="1" {{ old('allow_document_upload') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-600">Document Upload</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_payment_gateway" value="1" {{ old('allow_payment_gateway') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-600">Payment Gateway</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_custom_branding" value="1" {{ old('allow_custom_branding') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-600">Custom Branding</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_api_access" value="1" {{ old('allow_api_access') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-600">API Access</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_priority_support" value="1" {{ old('allow_priority_support') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-600">Priority Support</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('super-admin.plans.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700">
                    Create Plan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
