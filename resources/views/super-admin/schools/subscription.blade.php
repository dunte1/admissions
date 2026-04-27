@extends('layouts.super-admin')

@section('page-title', 'School Subscription - ' . $school->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('super-admin.schools.show', $school) }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to {{ $school->name }}
        </a>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Subscription Management</h1>
            <p class="text-gray-600">{{ $school->name }} ({{ $school->code }})</p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $school->subscription_status === 'active' ? 'bg-green-100 text-green-800' : ($school->subscription_status === 'trial' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
            {{ ucfirst($school->subscription_status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Current Subscription</h2>
                </div>
                <div class="p-6">
                    @if($currentSubscription)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-white rounded-lg border border-blue-100">
                            <div class="flex items-center">
                                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-gem text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-lg font-semibold text-gray-900">{{ $currentSubscription->plan->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $currentSubscription->plan->formatted_price }} / {{ $currentSubscription->plan->billing_period }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($currentSubscription->status) }}
                                </span>
                                <p class="text-sm text-gray-500 mt-1">{{ $currentSubscription->daysRemaining() }} days left</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-gem text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500">No active subscription</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Subscription History</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($subscriptions as $sub)
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-contract text-gray-500"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $sub->plan->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $sub->starts_at->format('M d, Y') }} - {{ $sub->expires_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sub->status_badge_class }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                                <a href="{{ route('super-admin.subscriptions.show', $sub) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            No subscription history found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                </div>
                <div class="p-4 space-y-3">
                    <button onclick="document.getElementById('createSubscriptionModal').classList.remove('hidden')" class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Create Subscription
                    </button>
                    
                    @if($currentSubscription && $currentSubscription->status !== 'cancelled')
                        <a href="{{ route('super-admin.subscriptions.show', $currentSubscription) }}" class="block w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center">
                            <i class="fas fa-external-link-alt mr-2"></i> View Details
                        </a>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Available Plans</h3>
                    <div class="space-y-2">
                        @foreach($plans as $plan)
                            <div class="p-2 bg-white rounded border border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ $plan->name }}</p>
                                <p class="text-xs text-gray-500">{{ $plan->formatted_price }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="createSubscriptionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('super-admin.schools.subscription.store', $school) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create Subscription</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-gray-700">Plan</label>
                            <select name="plan_id" id="plan_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                                <option value="">-- Select Plan --</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->formatted_price }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="duration_days" class="block text-sm font-medium text-gray-700">Duration (Days)</label>
                            <input type="number" name="duration_days" id="duration_days" value="30" min="1" max="365" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Create</button>
                    <button type="button" onclick="document.getElementById('createSubscriptionModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
