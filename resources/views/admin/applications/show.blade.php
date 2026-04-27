@extends('layouts.admin')

@section('title', 'Application Details - ' . $application->application_number)

@section('header', 'Application Details')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.applications.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $application->application_number }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Applied {{ $application->created_at->format('M d, Y \a\t h:i A') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.applications.pdf', $application->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
            @php
                $statusConfig = [
                    'draft' => ['bg-gray-100', 'text-gray-700', 'Draft'],
                    'pending' => ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                    'under_review' => ['bg-blue-100', 'text-blue-700', 'Under Review'],
                    'approved' => ['bg-green-100', 'text-green-700', 'Approved'],
                    'rejected' => ['bg-red-100', 'text-red-700', 'Rejected'],
                    'info_requested' => ['bg-orange-100', 'text-orange-700', 'Info Requested'],
                ];
                $status = $statusConfig[$application->status] ?? ['bg-gray-100', 'text-gray-700', ucfirst($application->status)];
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $status[0] }} {{ $status[1] }}">
                {{ $status[2] }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Applicant Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('labels.personal_info') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-6">
                        <div class="w-20 h-20 rounded-full overflow-hidden flex-shrink-0">
                            <x-user-avatar :user="$application->user" :size="80" class="" />
                        </div>
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $application->student->full_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->user->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->user->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->student->id_number ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</p>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($application->student->gender ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->student->date_of_birth?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Nationality</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $application->student->nationality ?? 'Kenyan' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Program Applied</p>
                                <p class="mt-1 text-sm font-medium text-indigo-600">{{ $application->program?->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Application Data --}}
            @if($applicationData)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Application Details
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    @if(isset($applicationData['personal']))
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Personal Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($applicationData['personal'] as $key => $value)
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                <p class="mt-1 text-sm text-gray-900">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($applicationData['academic']))
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">Academic Background</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($applicationData['academic'] as $key => $value)
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                <p class="mt-1 text-sm text-gray-900">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($applicationData['guardian']))
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b">{{ __('labels.guardian_info') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($applicationData['guardian'] as $key => $value)
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                <p class="mt-1 text-sm text-gray-900">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Documents --}}
            @if($application->documents && $application->documents->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Uploaded Documents
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($application->documents as $doc)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $doc->type ?? 'Document' }}</p>
                                <p class="text-xs text-gray-500">{{ $doc->created_at->format('M d, Y') }}</p>
                            </div>
                            <a href="#" class="p-2 text-gray-400 hover:text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Payments --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        {{ __('labels.payment_history') }}
                    </h2>
                </div>
                <div class="p-6">
                    @if($application->payments && $application->payments->count() > 0)
                    <div class="space-y-4">
                        @foreach($application->payments as $payment)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3 
                                    @if($payment->status === 'completed') bg-green-100
                                    @elseif($payment->status === 'manual_pending') bg-yellow-100
                                    @else bg-gray-100 @endif">
                                    <svg class="w-5 h-5 
                                        @if($payment->status === 'completed') text-green-600
                                        @elseif($payment->status === 'manual_pending') text-yellow-600
                                        @else text-gray-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ ucwords(str_replace('_', ' ', $payment->payment_type ?? 'Application Fee')) }}
                                        - KES {{ number_format($payment->amount) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} - 
                                        {{ $payment->paid_at?->format('M d, Y') ?? ($payment->created_at->format('M d, Y')) }}
                                    </p>
                                    @if($payment->receipt_number)
                                        <p class="text-xs text-gray-400">Receipt: {{ $payment->receipt_number }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($payment->status === 'manual_pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        Pending Verification
                                    </span>
                                    @can('verify_manual_payments')
                                    <button type="button" onclick="verifyPayment({{ $payment->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i> Verify
                                    </button>
                                    <button type="button" onclick="rejectPayment({{ $payment->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                        <i class="fas fa-times mr-1"></i> Reject
                                    </button>
                                    @endcan
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($payment->status === 'completed') bg-green-100 text-green-700
                                        @elseif($payment->status === 'failed') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                @endif
                                @if($payment->status === 'completed' && $payment->receipt_number)
                                <a href="{{ route('admin.payments.receipt', $payment->id) }}" target="_blank" 
                                    class="p-2 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg" title="View Receipt">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <p class="mt-2 text-sm">No payments recorded yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('labels.actions') }}</h2>
                </div>
                <div class="p-6">
                    @if(in_array($application->status, ['pending', 'under_review']))
                    <div class="space-y-4">
                        <form action="{{ route('admin.applications.approve', $application->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add approval notes..."></textarea>
                            </div>
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve Application
                            </button>
                        </form>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-500">or</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.applications.reject', $application->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason (required) *</label>
                                <textarea name="notes" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500" placeholder="Explain why..."></textarea>
                            </div>
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject Application
                            </button>
                        </form>

                        <form action="{{ route('admin.applications.request-info', $application->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Information Request (required) *</label>
                                <textarea name="notes" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="What information do you need?"></textarea>
                            </div>
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-medium transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Request Info
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $status[0] }} {{ $status[1] }}">
                            {{ $status[2] }}
                        </span>
                        @if($application->review_notes)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg text-left">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Review Notes</p>
                            <p class="text-sm text-gray-700">{{ $application->review_notes }}</p>
                        </div>
                        @endif
                        @if($application->reviewer)
                        <div class="mt-4 text-sm text-gray-500">
                            Reviewed by {{ $application->reviewer->fullName() }} on {{ $application->reviewed_at?->format('M d, Y') }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Timeline
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        @foreach($timeline as $index => $event)
                        <div class="flex items-start gap-4">
                            <div class="relative">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $event['completed'] ? 'bg-' . $event['color'] . '-100' : 'bg-gray-100' }}">
                                    @if($event['icon'] === 'document')
                                    <svg class="w-4 h-4 text-{{ $event['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    @elseif($event['icon'] === 'check')
                                    <svg class="w-4 h-4 text-{{ $event['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    @elseif($event['icon'] === 'x')
                                    <svg class="w-4 h-4 text-{{ $event['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4 text-{{ $event['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                <div class="absolute top-8 left-4 w-px h-6 bg-gray-200"></div>
                                @endif
                            </div>
                            <div class="flex-1 pb-6">
                                <p class="text-sm font-medium text-gray-900">{{ $event['title'] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $event['description'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $event['date']?->format('M d, Y h:i A') ?? '' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Payment Verification Modals --}}
<div id="verify-payment-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeVerifyModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Verify Payment</h3>
        <p class="text-gray-600 mb-4">Confirm manual payment verification for this application.</p>
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <p class="text-sm"><strong>Transaction ID:</strong> <span id="verify-transaction-id"></span></p>
            <p class="text-sm"><strong>Phone:</strong> <span id="verify-phone"></span></p>
        </div>
        <form id="verify-payment-form" method="POST">
            @csrf
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeVerifyModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Confirm Verification</button>
            </div>
        </form>
    </div>
</div>

<div id="reject-payment-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeRejectModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Payment</h3>
        <form id="reject-payment-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for rejection</label>
                <textarea name="reason" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" required placeholder="Enter reason..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function verifyPayment(paymentId) {
    fetch(`/admin/payments/${paymentId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('verify-transaction-id').textContent = data.transaction_id || 'N/A';
            document.getElementById('verify-phone').textContent = data.phone_number || 'N/A';
            document.getElementById('verify-payment-form').action = `/admin/payments/${paymentId}/verify`;
            document.getElementById('verify-payment-modal').classList.remove('hidden');
        });
}

function closeVerifyModal() {
    document.getElementById('verify-payment-modal').classList.add('hidden');
}

function rejectPayment(paymentId) {
    document.getElementById('reject-payment-form').action = `/admin/payments/${paymentId}/reject`;
    document.getElementById('reject-payment-modal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('reject-payment-modal').classList.add('hidden');
}
</script>
@endpush
