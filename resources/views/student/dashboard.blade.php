@extends('layouts.student')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('labels.welcome_back') }}, {{ auth()->user()->first_name }}!</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">{{ __('labels.track_applications') }} - {{ now()->format('l, F j, Y') }}</p>
            </div>
            <a href="{{ route('student.application.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] transition-all font-medium shadow-lg shadow-blue-200">
                <i class="fas fa-plus mr-2"></i>
                {{ __('labels.new_application') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
        <div class="stat-card bg-white rounded-xl shadow-sm p-3 sm:p-4 lg:p-6 border-l-4 border-purple-500 min-w-0">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('labels.total_applications') }}</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-purple-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-purple-600 text-sm sm:text-lg lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-center text-xs">
                <span class="text-gray-500">All time</span>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm p-3 sm:p-4 lg:p-6 border-l-4 border-yellow-500 min-w-0">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('labels.pending') }}</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-yellow-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock text-yellow-600 text-sm sm:text-lg lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-center text-xs">
                <span class="text-yellow-600">Awaiting</span>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm p-3 sm:p-4 lg:p-6 border-l-4 border-green-500 min-w-0">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('labels.approved') }}</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-green-600 mt-1">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-green-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-sm sm:text-lg lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-center text-xs">
                <span class="text-green-600">Admitted</span>
            </div>
        </div>

        <div class="stat-card bg-white rounded-xl shadow-sm p-3 sm:p-4 lg:p-6 border-l-4 border-blue-500 min-w-0">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">{{ __('labels.draft') }}</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-blue-600 mt-1">{{ $stats['draft'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-blue-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-edit text-blue-600 text-sm sm:text-lg lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-center text-xs">
                <span class="text-blue-600">In progress</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('labels.my_applications') }}</h2>
                @if($applications->count() > 0)
                <span class="text-sm text-gray-500">{{ $applications->count() }} {{ __('labels.applications') }}</span>
                @endif
            </div>

            @if($applications->isEmpty())
            <div class="p-8 sm:p-12 text-center">
                <div class="w-16 sm:w-20 h-16 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-alt text-gray-400 text-2xl sm:text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('labels.no_applications') }}</h3>
                <p class="text-gray-500 mb-6 max-w-sm mx-auto">{{ __('labels.start_application_prompt') }}</p>
                <a href="{{ route('student.application.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 w-full sm:w-auto bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] transition-all font-medium">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('labels.start_application') }}
                </a>
            </div>
            @else
            <div class="lg:hidden space-y-4 p-4">
                @foreach($applications as $application)
                @php
                    $statusConfig = [
                        'draft' => ['bg-gray-100 text-gray-700', 'Draft', 'far fa-edit'],
                        'pending' => ['bg-yellow-100 text-yellow-700', 'Pending', 'far fa-clock'],
                        'under_review' => ['bg-blue-100 text-blue-700', 'Under Review', 'far fa-search'],
                        'approved' => ['bg-green-100 text-green-700', 'Approved', 'far fa-check-circle'],
                        'rejected' => ['bg-red-100 text-red-700', 'Rejected', 'far fa-times-circle'],
                        'info_requested' => ['bg-orange-100 text-orange-700', 'Info Requested', 'far fa-question-circle'],
                    ];
                    $config = $statusConfig[$application->status] ?? ['bg-gray-100 text-gray-700', ucfirst($application->status), 'far fa-circle'];
                    $hasPayment = $application->payments()->where('status', 'completed')->exists();
                @endphp
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <a href="{{ route('student.application.show', $application->id) }}" class="font-semibold text-[#00008B] hover:text-[#1e40af] text-lg">
                            {{ $application->application_number }}
                        </a>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $config[0] }}">
                            <i class="{{ $config[2] }} mr-1"></i>
                            {{ $config[1] }}
                        </span>
                    </div>
                    <div class="mb-3">
                        @if($application->programName)
                        <p class="font-medium text-gray-900">{{ $application->programName }}</p>
                        <p class="text-sm text-gray-500">{{ $application->program->code ?? '' }}</p>
                        @else
                        <p class="font-medium text-gray-400">-</p>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-500">
                            @if($hasPayment)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check mr-1"></i> Paid
                            </span>
                            @elseif($application->status !== 'draft')
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                <i class="fas fa-minus mr-1"></i> -
                            </span>
                            @endif
                        </span>
                        <span class="text-xs text-gray-400">{{ $application->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        @if($application->status === 'draft')
                        <a href="{{ route('student.application.form', ['step' => $application->current_step ?? 'personal']) }}" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-2"></i> Continue
                        </a>
                        <a href="{{ route('student.payment.show', $application->id) }}" 
                           class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors">
                            <i class="fas fa-credit-card mr-2"></i> Pay Now
                        </a>
                        @else
                        <a href="{{ route('student.application.show', $application->id) }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] text-sm font-medium transition-colors">
                            <i class="fas fa-eye mr-2"></i> View Details
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.app_number') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.program') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.payment') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($applications as $application)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('student.application.show', $application->id) }}" class="font-medium text-[#00008B] hover:text-[#1e40af]">
                                    {{ $application->application_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if($application->programName)
                                <div class="font-medium text-gray-900">{{ $application->programName }}</div>
                                <div class="text-sm text-gray-500">{{ $application->program->code ?? '' }}</div>
                                @else
                                <div class="font-medium text-gray-400">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'draft' => ['bg-gray-100 text-gray-700', 'Draft', 'far fa-edit'],
                                        'pending' => ['bg-yellow-100 text-yellow-700', 'Pending', 'far fa-clock'],
                                        'under_review' => ['bg-blue-100 text-blue-700', 'Under Review', 'far fa-search'],
                                        'approved' => ['bg-green-100 text-green-700', 'Approved', 'far fa-check-circle'],
                                        'rejected' => ['bg-red-100 text-red-700', 'Rejected', 'far fa-times-circle'],
                                        'info_requested' => ['bg-orange-100 text-orange-700', 'Info Requested', 'far fa-question-circle'],
                                    ];
                                    $config = $statusConfig[$application->status] ?? ['bg-gray-100 text-gray-700', ucfirst($application->status), 'far fa-circle'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $config[0] }}">
                                    <i class="{{ $config[2] }} mr-1"></i>
                                    {{ $config[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $hasPayment = $application->payments()->where('status', 'completed')->exists();
                                @endphp
                                @if($hasPayment)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-check mr-1"></i> Paid
                                </span>
                                @elseif($application->status !== 'draft')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                    <i class="fas fa-minus mr-1"></i> -
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if($application->status === 'draft')
                                    <a href="{{ route('student.application.form', ['step' => $application->current_step ?? 'personal']) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                                        <i class="fas fa-edit mr-1"></i> {{ __('labels.continue') }}
                                    </a>
                                    <a href="{{ route('student.payment.show', $application->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors">
                                        <i class="fas fa-credit-card mr-1"></i> {{ __('labels.pay_now') }}
                                    </a>
                                    @else
                                    <a href="{{ route('student.application.show', $application->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] text-sm font-medium transition-colors">
                                        <i class="fas fa-eye mr-1"></i> {{ __('labels.view_details') }}
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div class="space-y-4 sm:space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                    Quick Actions
                </h3>
                <div class="space-y-2 sm:space-y-3">
                    <a href="{{ route('student.application.create') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                        <div class="w-10 h-10 bg-[#00008B] rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform flex-shrink-0">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">New Application</p>
                            <p class="text-xs text-gray-500">Apply for a new program</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('student.profile.edit') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform flex-shrink-0">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">Update Profile</p>
                            <p class="text-xs text-gray-500">Edit your information</p>
                        </div>
                    </a>

                    <a href="mailto:{{ contact_setting('contact_email', 'support@admissions.ac.ke') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform flex-shrink-0">
                            <i class="fas fa-headset text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">Get Help</p>
                            <p class="text-xs text-gray-500">Contact admissions</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 sm:mb-3 flex items-center">
                    <i class="fas fa-phone-alt text-green-500 mr-2"></i>
                    Need Help?
                </h3>
                <p class="text-sm text-gray-600 mb-3 sm:mb-4">
                    Our admissions team is ready to assist you with any questions.
                </p>
                <div class="space-y-2">
                    <a href="tel:{{ contact_setting('contact_phone', '+254700000000') }}" class="flex items-center text-sm text-gray-700 hover:text-[#00008B] transition-colors">
                        <i class="fas fa-phone w-6 text-gray-400"></i>
                        {{ contact_setting('contact_phone', '+254 700 000 000') }}
                    </a>
                    <a href="mailto:{{ contact_setting('contact_email', 'support@admissions.ac.ke') }}" class="flex items-center text-sm text-gray-700 hover:text-[#00008B] transition-colors">
                        <i class="fas fa-envelope w-6 text-gray-400"></i>
                        {{ contact_setting('contact_email', 'support@admissions.ac.ke') }}
                    </a>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-100 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-blue-900 mb-2 sm:mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                    Application Tips
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        Ensure all documents are clearly uploaded
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        Double-check your academic details
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        Pay application fee promptly
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        Check your email regularly for updates
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
