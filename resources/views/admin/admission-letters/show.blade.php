@extends('layouts.admin')

@section('page-title', 'Admission Letter - ' . ($letter->letter_number ?? ''))

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.admission-letters.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Letters
        </a>
        <div class="flex gap-3">
            <a href="{{ route('admin.admission-letters.download', $letter) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i> Download PDF
            </a>
            @if($letter->application)
                <a href="{{ route('admin.applications.show', $letter->application) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-file-alt mr-2"></i> View Application
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Admission Letter</h1>
                            <p class="text-sm text-gray-500">{{ $letter->letter_number ?? 'N/A' }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $letter->status === 'issued' ? 'bg-green-100 text-green-800' : ($letter->status === 'accepted' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($letter->status) }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Type</p>
                                <p class="text-lg font-semibold text-gray-900 capitalize">{{ $letter->type ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Issue Date</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $letter->issue_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if($letter->application)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-4">Applicant Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Name</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $letter->application->first_name }} {{ $letter->application->last_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $letter->application->email }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Program</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $letter->application->program->name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Intake</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $letter->application->intake->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($letter->response_deadline)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Response Deadline</h3>
                            <p class="text-lg font-semibold {{ $letter->response_deadline->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $letter->response_deadline->format('M d, Y') }}
                                @if($letter->response_deadline->isPast() && $letter->status === 'issued')
                                    <span class="text-sm font-normal text-red-500">(Expired)</span>
                                @endif
                            </p>
                        </div>
                        @endif

                        @if($letter->additional_conditions)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Additional Conditions</h3>
                            <p class="text-sm text-gray-700">{{ $letter->additional_conditions }}</p>
                        </div>
                        @endif

                        @if($letter->remarks)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Remarks</h3>
                            <p class="text-sm text-gray-700">{{ $letter->remarks }}</p>
                        </div>
                        @endif

                        @if($letter->responded_at)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Response Date</h3>
                            <p class="text-sm text-gray-700">{{ $letter->responded_at->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                </div>
                <div class="p-4 space-y-3">
                    @if($letter->status === 'issued')
                        <button onclick="document.getElementById('resendModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-paper-plane mr-2"></i> Resend Letter
                        </button>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Details</h2>
                </div>
                <div class="p-4">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Created</dt>
                            <dd class="font-medium text-gray-900">{{ $letter->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Last Updated</dt>
                            <dd class="font-medium text-gray-900">{{ $letter->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="resendModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <form action="{{ route('admin.admission-letters.resend', $letter) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Resend Admission Letter</h3>
                    <p class="mt-2 text-sm text-gray-500">This will send the letter to the applicant's email address.</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Send</button>
                    <button type="button" onclick="document.getElementById('resendModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
