@extends('layouts.student')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Review Application</h1>
                <p class="text-gray-600 mt-1">Application #{{ $application->application_number }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold 
                    @if($application->status === 'approved') bg-green-100 text-green-700
                    @elseif($application->status === 'rejected') bg-red-100 text-red-700
                    @elseif($application->status === 'pending') bg-yellow-100 text-yellow-700
                    @else bg-gray-100 text-gray-700 @endif">
                    <i class="fas fa-circle mr-2 text-xs"></i>
                    {{ ucfirst($application->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-clock text-blue-500 mr-2"></i>
            Application Timeline
        </h3>
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Application Started</p>
                        <p class="text-sm text-gray-500">{{ $application->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex-1 text-center">
                @if($application->submitted_at)
                <div class="flex items-center">
                    <div class="w-full h-1 bg-green-500 rounded"></div>
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-paper-plane text-green-600"></i>
                    </div>
                    <div class="w-full h-1 bg-green-500 rounded"></div>
                </div>
                <p class="font-semibold text-gray-900 mt-2">Submitted</p>
                <p class="text-sm text-gray-500">{{ $application->submitted_at->format('M d, Y') }}</p>
                @else
                <div class="flex items-center">
                    <div class="w-full h-1 bg-gray-200 rounded"></div>
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-clock text-gray-400"></i>
                    </div>
                    <div class="w-full h-1 bg-gray-200 rounded"></div>
                </div>
                <p class="font-semibold text-gray-400 mt-2">Pending</p>
                @endif
            </div>
            <div class="flex-1 text-right">
                @if(in_array($application->status, ['approved', 'rejected', 'under_review']))
                <div class="flex items-center justify-end">
                    <div class="w-10 h-10 rounded-full @if($application->status === 'approved') bg-green-100 @else bg-yellow-100 @endif flex items-center justify-center">
                        <i class="fas @if($application->status === 'approved') fa-check-circle text-green-600 @else fa-search text-yellow-600 @endif"></i>
                    </div>
                    <div class="mr-4 text-right">
                        <p class="font-semibold text-gray-900">Under Review</p>
                        <p class="text-sm text-gray-500">{{ $application->reviewed_at?->format('M d, Y') ?? 'Processing' }}</p>
                    </div>
                </div>
                @else
                <div class="flex items-center justify-end">
                    <div class="w-full h-1 bg-gray-200 rounded"></div>
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-clock text-gray-400"></i>
                    </div>
                </div>
                <p class="font-semibold text-gray-400 mt-2">Review Pending</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Application Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Program --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-graduation-cap text-purple-500 mr-2"></i>
                    Program Applied
                </h3>
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-book text-purple-600"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $application->program->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $application->program->department->name ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- Personal Info --}}
            @if(isset($formData['personal']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user text-blue-500 mr-2"></i>
                    Personal Information
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Full Name</p>
                        <p class="font-medium text-gray-900">{{ $student->full_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Gender</p>
                        <p class="font-medium text-gray-900">{{ ucfirst($formData['personal']['gender'] ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Date of Birth</p>
                        <p class="font-medium text-gray-900">{{ $formData['personal']['date_of_birth'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Nationality</p>
                        <p class="font-medium text-gray-900">{{ $formData['personal']['nationality'] ?? 'Kenyan' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">ID Number</p>
                        <p class="font-medium text-gray-900">{{ $formData['personal']['id_number'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Phone</p>
                        <p class="font-medium text-gray-900">{{ $formData['personal']['phone'] ?? auth()->user()->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">County</p>
                        <p class="font-medium text-gray-900">{{ $formData['personal']['county'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">City</p>
                        <p class="font-medium text-gray-900">{{ $formData['personal']['city'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Academic --}}
            @if(isset($formData['academic']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-graduation-cap text-green-500 mr-2"></i>
                    Academic Background
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Level</p>
                        <p class="font-medium text-gray-900">{{ strtoupper($formData['academic']['education_level'] ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Institution</p>
                        <p class="font-medium text-gray-900">{{ $formData['academic']['institution_name'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Year Completed</p>
                        <p class="font-medium text-gray-900">{{ $formData['academic']['year_of_completion'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Grade</p>
                        <p class="font-medium text-gray-900">{{ $formData['academic']['average_grade'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Guardian --}}
            @if(isset($formData['guardian']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-users text-orange-500 mr-2"></i>
                    Guardian / Next of Kin
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Name</p>
                        <p class="font-medium text-gray-900">{{ $formData['guardian']['guardian_name'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Relationship</p>
                        <p class="font-medium text-gray-900">{{ ucfirst($formData['guardian']['guardian_relationship'] ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Phone</p>
                        <p class="font-medium text-gray-900">{{ $formData['guardian']['guardian_phone'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Email</p>
                        <p class="font-medium text-gray-900">{{ $formData['guardian']['guardian_email'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Financial --}}
            @if(isset($formData['financial']))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-wallet text-emerald-500 mr-2"></i>
                    Financial Information
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Sponsorship Type</p>
                        <p class="font-medium text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if(($formData['financial']['sponsorship_type'] ?? '') === 'self') bg-blue-100 text-blue-700
                                @elseif(($formData['financial']['sponsorship_type'] ?? '') === 'government') bg-red-100 text-red-700
                                @else bg-purple-100 text-purple-700 @endif">
                                {{ ucfirst(str_replace('_', ' ', $formData['financial']['sponsorship_type'] ?? 'N/A')) }}
                            </span>
                        </p>
                    </div>
                    @if(isset($formData['financial']['sponsor_name']))
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Sponsor Name</p>
                        <p class="font-medium text-gray-900">{{ $formData['financial']['sponsor_name'] ?? 'N/A' }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Documents Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-folder text-yellow-500 mr-2"></i>
                    Documents
                </h3>
                <div class="space-y-3">
                    @php
                        $docTypes = [
                            'id_document' => 'ID Document',
                            'certificate' => 'Certificate',
                            'photo' => 'Passport Photo',
                            'birth_certificate' => 'Birth Certificate',
                            'results_slip' => 'Results Slip',
                        ];
                    @endphp
                    @forelse($application->documents as $doc)
                    <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">{{ $docTypes[$doc->type] ?? $doc->type }}</span>
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    @empty
                    @foreach($docTypes as $type => $label)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-500">{{ $label }}</span>
                        <i class="fas fa-times-circle text-gray-400"></i>
                    </div>
                    @endforeach
                    @endforelse
                </div>
            </div>

            {{-- Actions --}}
            @if($application->status === 'draft')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('student.application.form', ['step' => 'personal']) }}" class="block w-full text-center bg-[#00008B] text-white py-3 rounded-lg hover:bg-[#1e40af] transition-all font-semibold">
                        <i class="fas fa-edit mr-2"></i>
                        Continue Application
                    </a>
                    <p class="text-xs text-center text-gray-500 mt-2">
                        Application fee: KES 1,000 (Payable after submission)
                    </p>
                </div>
            </div>
            @endif

            {{-- Help --}}
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
                <h3 class="text-lg font-bold text-blue-900 mb-2">
                    <i class="fas fa-question-circle mr-2"></i>
                    Need Help?
                </h3>
                <p class="text-sm text-blue-700 mb-4">
                    Contact our admissions team for assistance with your application.
                </p>
                <a href="mailto:{{ system_setting('contact_email', 'support@admissions.ac.ke') }}" class="text-sm font-medium text-blue-900 hover:text-blue-700">
                    {{ system_setting('contact_email', 'support@admissions.ac.ke') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
