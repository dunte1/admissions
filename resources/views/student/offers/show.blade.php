@extends('layouts.student')

@section('title', __('Offer Details'))

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Admission Offer') }}</h1>
                <p class="text-gray-600 mt-1">Letter #{{ $letter->letter_number }}</p>
            </div>
            <a href="{{ route('student.offers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Offers
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Offer Status Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 
                    @if($letter->type === 'admission') bg-gradient-to-r from-green-50 to-emerald-50
                    @elseif($letter->type === 'rejection') bg-gradient-to-r from-red-50 to-orange-50
                    @else bg-gradient-to-r from-yellow-50 to-amber-50 @endif">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center
                            @if($letter->type === 'admission') bg-green-100
                            @elseif($letter->type === 'rejection') bg-red-100
                            @else bg-yellow-100 @endif">
                            @if($letter->type === 'admission')
                                <i class="fas fa-award text-3xl text-green-600"></i>
                            @elseif($letter->type === 'rejection')
                                <i class="fas fa-times-circle text-3xl text-red-600"></i>
                            @else
                                <i class="fas fa-clock text-3xl text-yellow-600"></i>
                            @endif
                        </div>
                        <div>
                            @if($letter->type === 'admission')
                                <h2 class="text-2xl font-bold text-green-800">Congratulations!</h2>
                                <p class="text-green-700">You have been admitted to our program.</p>
                            @elseif($letter->type === 'rejection')
                                <h2 class="text-2xl font-bold text-red-800">Application Status</h2>
                                <p class="text-red-700">After careful review, we regret that we cannot offer you admission.</p>
                            @else
                                <h2 class="text-2xl font-bold text-yellow-800">Provisional Offer</h2>
                                <p class="text-yellow-700">Your admission is pending additional conditions.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-alt text-[#00008B] mr-2"></i>
                        Offer Details
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Letter Reference</p>
                            <p class="font-semibold text-gray-900">{{ $letter->letter_number }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Date Issued</p>
                            <p class="font-semibold text-gray-900">{{ $letter->issue_date->format('F d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Program</p>
                            @if($letter->application->program)
                            <p class="font-semibold text-gray-900">{{ $letter->application->program->name }}</p>
                            @else
                            <p class="font-semibold text-gray-400">-</p>
                            @endif
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Application #</p>
                            <p class="font-semibold text-gray-900">{{ $letter->application->application_number }}</p>
                        </div>
                    </div>

                    @if($letter->additional_conditions)
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Conditions
                        </h4>
                        <p class="text-blue-700">{{ $letter->additional_conditions }}</p>
                    </div>
                    @endif

                    @if($letter->response_deadline && !$letter->isExpired() && $letter->type === 'admission')
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <h4 class="font-semibold text-yellow-800 mb-2 flex items-center">
                            <i class="fas fa-clock mr-2"></i>
                            Response Required
                        </h4>
                        <p class="text-yellow-700">Please respond by <strong>{{ $letter->response_deadline->format('F d, Y') }}</strong> to secure your place.</p>
                    </div>
                    @endif

                    @if($letter->response_deadline && $letter->isExpired())
                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <h4 class="font-semibold text-red-800 mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Deadline Passed
                        </h4>
                        <p class="text-red-700">The response deadline has passed. Please contact the admissions office immediately.</p>
                    </div>
                    @endif

                    @if($letter->remarks)
                    <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-xl">
                        <h4 class="font-semibold text-gray-800 mb-2">Additional Notes</h4>
                        <p class="text-gray-600">{{ $letter->remarks }}</p>
                    </div>
                    @endif
                </div>

                {{-- Response Actions --}}
                @if($letter->isPending() && !$letter->isExpired() && $letter->type === 'admission')
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Respond to Offer</h3>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form action="{{ route('student.offers.accept', $letter->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold transition-colors" onclick="return confirm('Are you sure you want to accept this offer?')">
                                <i class="fas fa-check mr-2"></i>
                                Accept Offer
                            </button>
                        </form>
                        <button type="button" x-data="{ showDeclineModal: false }" @click="showDeclineModal = true" class="sm:w-auto px-6 py-3 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Decline
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Download Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-download text-[#00008B] mr-2"></i>
                    Document Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('student.offers.preview', $letter->id) }}" target="_blank" class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        View / Preview
                    </a>
                    <a href="{{ route('student.offers.download', $letter->id) }}" class="w-full inline-flex items-center justify-center px-5 py-2.5 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- Help Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-headset text-[#00008B] mr-2"></i>
                    Need Help?
                </h3>
                <p class="text-sm text-gray-600 mb-4">Contact the admissions office if you have any questions:</p>
                <div class="space-y-3">
                    <a href="mailto:{{ system_setting('contact_email', 'support@admissions.ac.ke') }}" class="flex items-center text-sm text-gray-700 hover:text-[#00008B] transition-colors">
                        <i class="fas fa-envelope w-6 text-gray-400"></i>
                        {{ system_setting('contact_email', 'support@admissions.ac.ke') }}
                    </a>
                    <a href="tel:{{ system_setting('contact_phone', '+254700000000') }}" class="flex items-center text-sm text-gray-700 hover:text-[#00008B] transition-colors">
                        <i class="fas fa-phone w-6 text-gray-400"></i>
                        {{ system_setting('contact_phone', '+254 700 000 000') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Decline Modal --}}
<div x-data="{ showDeclineModal: false }" 
     x-show="showDeclineModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true" x-on:click="showDeclineModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg w-full"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <form action="{{ route('student.offers.decline', $letter->id) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4 sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Decline Offer</h3>
                            <div class="mt-2">
                                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <strong>Warning:</strong> This action cannot be undone. Are you sure you want to decline this offer?
                                    </p>
                                </div>
                                <div>
                                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason (Optional)</label>
                                    <textarea name="reason" id="reason" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="3" placeholder="Please let us know why you're declining..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Confirm Decline
                    </button>
                    <button type="button" x-on:click="showDeclineModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
