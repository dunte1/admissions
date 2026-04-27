@extends('layouts.student')

@section('content')
<div class="max-w-md mx-auto text-center py-12">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h1>
        <p class="text-gray-600 mb-6">{{ $message ?? 'Unfortunately, your payment could not be processed. Please try again.' }}</p>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-500">Application Number</p>
            <p class="text-lg font-semibold text-purple-600">{{ $application->application_number }}</p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('student.payment.show', $application->id) }}" 
               class="block w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700">
                Try Again
            </a>
            <a href="{{ route('student.dashboard') }}" 
               class="block w-full bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200">
                Return to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
