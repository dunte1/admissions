@extends('layouts.super-admin')

@section('title', $application->application_number)

@section('header', 'Application Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super-admin.applications.index') }}">Applications</a></li>
    <li class="breadcrumb-item active">{{ $application->application_number }}</li>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Application #{{ $application->application_number }}</h2>
                            <p class="text-sm text-gray-500">Applied {{ $application->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($application->status == 'approved') bg-green-100 text-green-800
                        @elseif($application->status == 'rejected') bg-red-100 text-red-800
                        @elseif($application->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst($application->status) }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-3 flex items-center">
                            <i class="fas fa-user mr-2 text-purple-600"></i> Student Information
                        </h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Name:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $application->student?->user?->fullName() ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Email:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $application->student?->user?->email ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Phone:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $application->student?->user?->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-3 flex items-center">
                            <i class="fas fa-graduation-cap mr-2 text-purple-600"></i> Application Details
                        </h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">School:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $application->school?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Program:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $application->program?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Intake:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $application->intake?->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($application->student)
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-500 mb-3 flex items-center">
                        <i class="fas fa-id-card mr-2 text-purple-600"></i> Personal Details
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Date of Birth</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $application->student->date_of_birth ? Carbon\Carbon::parse($application->student->date_of_birth)->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Gender</p>
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst($application->student->gender ?? 'N/A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Nationality</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->student->nationality ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">ID/Passport</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->student->id_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($application->student?->address)
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-500 mb-3 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-purple-600"></i> Address
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-900">
                            {{ $application->student->address->postal_address ?? 'N/A' }}, {{ $application->student->address->city ?? '' }}
                        </p>
                    </div>
                </div>
                @endif

                @if($application->student?->guardian)
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-500 mb-3 flex items-center">
                        <i class="fas fa-users mr-2 text-purple-600"></i> Guardian/Parent Information
                    </h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Name</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->student->guardian->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Phone</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->student->guardian->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Email</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->student->guardian->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($application->documents->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-folder-open mr-2 text-purple-600"></i> Uploaded Documents
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($application->documents as $document)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $document->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst(str_replace('_', ' ', $document->type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @switch($document->status)
                                        @case('verified')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Verified</span>
                                            @break
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ ucfirst($document->status) }}</span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $document->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($application->payments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-credit-card mr-2 text-purple-600"></i> Payments
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($application->payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $payment->transaction_id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($payment->status == 'completed') bg-green-100 text-green-800
                                        @elseif($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-history mr-2 text-purple-600"></i> Timeline
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($timeline as $event)
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-3
                                @if($event['type'] == 'submission') bg-purple-100
                                @elseif($event['type'] == 'approved') bg-green-100
                                @elseif($event['type'] == 'rejected') bg-red-100
                                @else bg-blue-100 @endif">
                                <i class="fas 
                                    @if($event['type'] == 'submission') fa-paper-plane text-purple-600
                                    @elseif($event['type'] == 'approved') fa-check text-green-600
                                    @elseif($event['type'] == 'rejected') fa-times text-red-600
                                    @else fa-eye text-blue-600 @endif text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $event['event'] }}</p>
                                <p class="text-xs text-gray-500">{{ $event['date']->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-bolt mr-2 text-purple-600"></i> Quick Actions
                </h3>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('super-admin.applications.index') }}" class="flex items-center justify-center w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
                @if($application->status === 'pending')
                    <form action="{{ route('super-admin.applications.approve', $application) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center justify-center w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i> Approve Application
                        </button>
                    </form>
                    <form action="{{ route('super-admin.applications.reject', $application) }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center justify-center w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-2"></i> Reject Application
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
