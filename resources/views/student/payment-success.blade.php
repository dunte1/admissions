@extends('layouts.student')

@section('content')
<div class="max-w-md mx-auto text-center py-12">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
        <p class="text-gray-600 mb-6">Your payment for application {{ $application->application_number }} has been processed successfully.</p>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-500">Application Number</p>
            <p class="text-lg font-semibold text-purple-600">{{ $application->application_number }}</p>
        </div>
        
        @if(isset($receipt) && $receipt)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-500">M-PESA Receipt</p>
            <p class="text-lg font-semibold text-green-700">{{ $receipt }}</p>
        </div>
        @endif

        @if($payment && $payment->status === 'completed' && $payment->receipt_number)
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-500">Official Receipt</p>
            <p class="text-lg font-semibold text-purple-700">{{ $payment->receipt_number }}</p>
            <a href="{{ route('student.payment.receipt', $payment->id) }}" 
               class="mt-3 inline-flex items-center justify-center w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download Receipt
            </a>
        </div>
        @endif

        <div class="space-y-3">
            <a href="{{ route('student.application.form', ['step' => 'documents']) }}" 
               class="block w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700">
                Continue Application
            </a>
            <a href="{{ route('student.dashboard') }}" 
               class="block w-full bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200">
                Return to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
