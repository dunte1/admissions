@extends('layouts.student')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $application->application_number }}</h1>
            @if($application->programName)
            <p class="text-gray-500 mt-1">{{ $application->programName }}</p>
            @endif
        </div>
        <span class="px-4 py-2 text-sm rounded-full w-fit {{ $application->getStatusBadgeClass() }}">
            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('labels.application_details') }}</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    @if($application->programName)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Program</h3>
                        <p class="text-gray-900 font-medium">{{ $application->programName }}</p>
                    </div>
                    @endif
                    @if($application->program?->level)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Level</h3>
                        <p class="text-gray-900">{{ ucfirst($application->program->level) }}</p>
                    </div>
                    @endif
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Applied Date</h3>
                        <p class="text-gray-900">{{ $application->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                        <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</p>
                    </div>
                </div>

                @if($application->review_notes)
                <div class="mt-4 sm:mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="text-sm font-medium text-yellow-800">Review Notes</h3>
                    <p class="text-sm yellow-700 mt-1">{{ $application->review_notes }}</p>
                </div>
                @endif
            </div>
        <span class="px-4 py-2 text-sm rounded-full w-fit {{ $application->getStatusBadgeClass() }}">
            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('labels.application_details') }}</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Program</h3>
                        <p class="text-gray-900 font-medium">{{ $application->programName ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Level</h3>
                        <p class="text-gray-900">{{ ucfirst($application->program->level ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Applied Date</h3>
                        <p class="text-gray-900">{{ $application->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                        <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</p>
                    </div>
                </div>

                @if($application->review_notes)
                <div class="mt-4 sm:mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h3 class="text-sm font-medium text-yellow-800">Review Notes</h3>
                    <p class="text-sm text-yellow-700 mt-1">{{ $application->review_notes }}</p>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <h2 class="text-lg font-semibold mb-4">{{ __('labels.payment_history') }}</h2>
                
                @php
                    $admissionPayment = $application->payments->where('payment_type', 'admission_fee')->first();
                    $commitmentPayment = $application->payments->where('payment_type', 'commitment_fee')->first();
                    $showCommitment = in_array($application->status, ['offered', 'accepted']);
                    $completedPayment = $application->payments->where('status', 'completed')->first();
                @endphp
                
                @if($application->payments->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-gray-500 mb-3">No payments recorded</p>
                        <a href="{{ route('student.payment.show', $application->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                            <i class="fas fa-credit-card mr-2"></i> Pay Now
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        {{-- Admission Fee --}}
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $admissionPayment && $admissionPayment->status === 'completed' ? 'bg-green-100' : 'bg-gray-100' }}">
                                    <i class="fas fa-file-invoice-dollar {{ $admissionPayment && $admissionPayment->status === 'completed' ? 'text-green-600' : 'text-gray-400' }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Admission Fee</p>
                                    <p class="text-xs text-gray-500">KES {{ number_format($admissionPayment?->amount ?? 2000) }}</p>
                                </div>
                            </div>
                            @if($admissionPayment && $admissionPayment->status === 'completed')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Paid</span>
                            @else
                                <a href="{{ route('student.payment.show', $application->id) }}" class="text-xs text-green-600 hover:text-green-700">Pay Now</a>
                            @endif
                        </div>
                        
                        {{-- Commitment Fee (if applicable) --}}
                        @if($showCommitment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $commitmentPayment && $commitmentPayment->status === 'completed' ? 'bg-green-100' : 'bg-blue-100' }}">
                                    <i class="fas fa-handshake {{ $commitmentPayment && $commitmentPayment->status === 'completed' ? 'text-green-600' : 'text-blue-400' }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Commitment Fee</p>
                                    <p class="text-xs text-gray-500">KES {{ number_format($commitmentPayment?->amount ?? 5000) }}</p>
                                </div>
                            </div>
                            @if($commitmentPayment && $commitmentPayment->status === 'completed')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Paid</span>
                            @else
                                <a href="{{ route('student.payment.show', $application->id) }}" class="text-xs text-blue-600 hover:text-blue-700">Pay Now</a>
                            @endif
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('student.payment.show', $application->id) }}" class="text-sm text-purple-600 hover:text-purple-700">
                            View all payments <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4 sm:space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold mb-4">{{ __('labels.quick_actions') }}</h3>
                
                @if($application->status === 'draft')
                    <a href="{{ route('student.application.form', ['step' => $application->current_step ?? 'personal']) }}" 
                       class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 mb-3 font-medium">
                        Continue Application
                    </a>
                @endif

                @if($application->status === 'draft' && !$application->payments->where('status', 'completed')->exists())
                    <a href="{{ route('student.payment.show', $application->id) }}" 
                       class="block w-full bg-green-600 text-white text-center py-3 rounded-lg hover:bg-green-700 mb-3 font-medium">
                        Pay Application Fee
                    </a>
                @endif

                                @if($completedPayment)
                    <a href="{{ route('student.payment.receipt', $completedPayment->id) }}" target="_blank" class="block w-full bg-gray-100 text-gray-700 text-center py-3 rounded-lg hover:bg-gray-200 font-medium">
                        <i class="fas fa-download mr-2"></i> Download Receipt
                    </a>
                @else
                    <span class="block w-full bg-gray-50 text-gray-400 text-center py-3 rounded-lg font-medium cursor-not-allowed">
                        <i class="fas fa-download mr-2"></i> No Receipt Available
                    </span>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6">
                <h3 class="text-lg font-semibold mb-4">{{ __('labels.need_help') }}</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Contact our admissions office for assistance with your application.
                </p>
                <div class="space-y-2 text-sm">
                    <p>
                        <strong class="text-gray-900">Email:</strong> 
                        <a href="mailto:{{ contact_setting('contact_email', 'support@example.com') }}" class="text-[#00008B] hover:underline">
                            {{ contact_setting('contact_email', 'support@example.com') }}
                        </a>
                    </p>
                    <p>
                        <strong class="text-gray-900">Phone:</strong> 
                        <a href="tel:{{ contact_setting('contact_phone', '+254700000000') }}" class="text-[#00008B] hover:underline">
                            {{ contact_setting('contact_phone', '+254 700 000 000') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
