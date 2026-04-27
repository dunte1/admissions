@extends('layouts.super-admin')

@section('page-title', 'Subscription Details - ' . $subscription->school->name)

@section('content')
<div class="min-h-screen bg-gray-50 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <a href="{{ route('super-admin.subscriptions.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2 text-xs"></i>
                Back to Subscriptions
            </a>
        </div>

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $subscription->school->name }}</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $subscription->school->code }} &bull; {{ ucfirst($subscription->status) }} Subscription</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $subscription->status_badge_class }}">
                <i class="fas fa-circle text-xs mr-1.5"></i>
                {{ ucfirst($subscription->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-900">{{ $subscription->plan->name }} Plan</h2>
                                <p class="text-sm text-gray-500">{{ $subscription->plan->formatted_price }} / {{ $subscription->plan->billing_period_label }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Max Students</p>
                                <p class="text-lg font-bold text-gray-900">{{ $subscription->plan->max_students ?? '∞' }}</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Max Staff</p>
                                <p class="text-lg font-bold text-gray-900">{{ $subscription->plan->max_staff ?? '∞' }}</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Max Programs</p>
                                <p class="text-lg font-bold text-gray-900">{{ $subscription->plan->max_programs ?? '∞' }}</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Max Applications</p>
                                <p class="text-lg font-bold text-gray-900">{{ $subscription->plan->max_applications ?? '∞' }}</p>
                            </div>
                        </div>

                        @php
                            $features = $subscription->plan->features;
                            if (is_string($features)) {
                                $features = json_decode($features, true) ?? [];
                            }
                        @endphp
                        @if($features && is_array($features) && count($features) > 0)
                        <div class="mt-6">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Plan Features</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($features as $feature)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                                        <i class="fas fa-check text-xs mr-1"></i>
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-lg font-semibold text-gray-900">Subscription Timeline</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="text-center flex-1">
                                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-green-100 text-green-600 mb-2">
                                    <i class="fas fa-play"></i>
                                </div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Started</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $subscription->starts_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex-1 px-4">
                                <div class="relative">
                                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                                        <div style="width: {{ max(0, min(100, 100 - ($subscription->daysRemaining() / max(1, $subscription->plan->duration_days) * 100))) }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
                                    </div>
                                    <div class="absolute top-4 left-0 right-0 text-center">
                                        <span class="text-xs font-medium text-gray-500">{{ $subscription->daysRemaining() }} days remaining</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center flex-1">
                                <div class="inline-flex items-center justify-center h-12 w-12 rounded-full {{ $subscription->isExpired() ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }} mb-2">
                                    <i class="fas fa-flag"></i>
                                </div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $subscription->expires_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($subscription->school->status === 'suspended')
                <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-red-800">Account Suspended</h3>
                            <p class="text-sm text-red-600">This school's account has been suspended. Contact support for assistance.</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($subscription->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-lg font-semibold text-gray-900">Notes</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600">{{ $subscription->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-24">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <a href="{{ route('super-admin.schools.show', $subscription->school) }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-building mr-2"></i>
                            View School
                        </a>

                        @if($subscription->status !== 'cancelled')
                            <button type="button" onclick="document.getElementById('extendModal').classList.remove('hidden')" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Extend Subscription
                            </button>

                            <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                                <i class="fas fa-ban mr-2"></i>
                                Cancel Subscription
                            </button>
                        @else
                            <form action="{{ route('super-admin.subscriptions.reactivate', $subscription) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Reactivate Subscription
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Details</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Payment</dt>
                                <dd class="font-medium text-gray-900">${{ number_format($subscription->amount_paid, 2) }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Method</dt>
                                <dd class="font-medium text-gray-900 capitalize">{{ $subscription->payment_method ?? 'N/A' }}</dd>
                            </div>
                            @if($subscription->cancelled_at)
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Cancelled</dt>
                                <dd class="font-medium text-red-600">{{ $subscription->cancelled_at->format('M d, Y') }}</dd>
                            </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Created</dt>
                                <dd class="font-medium text-gray-900">{{ $subscription->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="extendModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <form action="{{ route('super-admin.subscriptions.extend', $subscription) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-calendar-plus text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Extend Subscription</h3>
                            <p class="mt-2 text-sm text-gray-500">Add additional days to this subscription.</p>
                        </div>
                    </div>
                    <div class="mt-5">
                        <label for="days" class="block text-sm font-medium text-gray-700">Number of Days</label>
                        <input type="number" name="days" id="days" min="1" max="365" value="30" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border" required>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Extend</button>
                    <button type="button" onclick="document.getElementById('extendModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="cancelModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <form action="{{ route('super-admin.subscriptions.cancel', $subscription) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Cancel Subscription</h3>
                            <p class="mt-2 text-sm text-gray-500">Are you sure you want to cancel this subscription? This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Cancel Subscription</button>
                    <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Keep Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
