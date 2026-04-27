@extends('layouts.super-admin')

@section('title', 'Plans Management')

@section('header', 'Plans Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Subscription Plans</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your subscription plans</p>
        </div>
        <a href="{{ route('super-admin.plans.create') }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">
            Create Plan
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($plans as $plan)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden {{ !$plan->is_active ? 'opacity-60' : '' }}">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                    @if(!$plan->is_active)
                    <span class="px-2 py-1 text-xs font-medium text-gray-600 bg-gray-200 rounded">Inactive</span>
                    @endif
                </div>
                
                <div class="mb-4">
                    <span class="text-3xl font-bold text-gray-900">${{ number_format($plan->price, 0) }}</span>
                    <span class="text-gray-500">/{{ $plan->billing_period }}</span>
                </div>

                <p class="text-sm text-gray-500 mb-4">{{ $plan->description }}</p>

                <ul class="space-y-2 text-sm text-gray-600 mb-6">
                    <li class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $plan->max_students ?? 'Unlimited' }} Students
                    </li>
                    <li class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $plan->max_programs ?? 'Unlimited' }} Programs
                    </li>
                </ul>

                <div class="flex gap-2">
                    <a href="{{ route('super-admin.plans.edit', $plan) }}" class="flex-1 px-3 py-2 text-center text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Edit
                    </a>
                    @if($plan->subscriptions()->count() === 0)
                    <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-red-600 bg-red-100 rounded-md hover:bg-red-200">
                            Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center py-12 text-gray-500">
            No plans found. <a href="{{ route('super-admin.plans.create') }}" class="text-purple-600 hover:text-purple-500">Create one</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
