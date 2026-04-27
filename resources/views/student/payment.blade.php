@extends('layouts.student')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('labels.payment') }}</h1>
                <p class="text-gray-600 mt-1">Application #{{ $application->application_number }}</p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success') && session('waiting_payment'))
        <div id="payment-success-banner" class="mb-6 bg-green-50 border border-green-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-green-800">Payment Request Sent!</h3>
                    <p class="text-green-700 mt-1">Check your phone and enter your M-PESA PIN to complete the payment.</p>
                    
                    <div id="payment-status-container" class="mt-4">
                        <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-green-200">
                            <div>
                                <p class="text-sm text-gray-600" id="payment-status-message">Waiting for payment confirmation...</p>
                                <p class="text-xs text-gray-400 mt-1" id="payment-timer">0 seconds</p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="checkPaymentStatus()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                                    <i class="fas fa-sync mr-2"></i>Check Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('success') && !session('waiting_payment'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Admission Fee Section --}}
            <div class="bg-white rounded-xl shadow-sm border {{ $admissionPayment && $admissionPayment->status === 'completed' ? 'border-green-200' : 'border-gray-200' }} p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">{{ $paymentConfig['admission_fee']['label'] }}</h2>
                    @if($admissionPayment && $admissionPayment->status === 'completed')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-700">
                            <i class="fas fa-check-circle mr-1"></i> Paid
                        </span>
                    @elseif($admissionPayment && $admissionPayment->status === 'manual_pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700">
                            <i class="fas fa-clock mr-1"></i> Pending Verification
                        </span>
                    @endif
                </div>
                
                <p class="text-gray-600 text-sm mb-4">{{ $paymentConfig['admission_fee']['description'] }}</p>
                
                <div class="text-2xl font-bold text-gray-900 mb-6">
                    {{ $paymentConfig['admission_fee']['currency_symbol'] }}{{ number_format($paymentConfig['admission_fee']['amount']) }}
                </div>

                @if($admissionPayment && $admissionPayment->status === 'completed')
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Receipt Number</p>
                                <p class="font-semibold text-gray-900">{{ $admissionPayment->receipt_number }}</p>
                            </div>
                            <a href="{{ route('student.payment.receipt', $admissionPayment->id) }}" target="_blank" class="text-green-600 hover:text-green-700">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    </div>
                @else
                    {{-- STK Push Payment --}}
                    @if($paymentConfig['mpesa']['stk_enabled'])
                        <div class="mb-6">
                            <form action="{{ route('student.payment.initiate', $application->id) }}" method="POST" id="admission-payment-form" class="payment-form">
                                @csrf
                                <input type="hidden" name="payment_method" value="mpesa">
                                <input type="hidden" name="payment_type" value="admission_fee">
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">+254</span>
                                        <input type="tel" name="phone" id="admission-phone" 
                                            placeholder="712345678" 
                                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                            pattern="[0-9]{9}" maxlength="9" required
                                            value="{{ auth()->user()->phone ? str_replace('254', '', auth()->user()->phone) : '' }}">
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Enter 9 digits (e.g., 712345678)</p>
                                </div>

                                <button type="submit" class="w-full bg-green-600 text-white py-4 rounded-lg hover:bg-green-700 transition-all font-semibold text-lg flex items-center justify-center gap-2 stk-push-btn">
                                    <i class="fas fa-mobile-alt"></i>
                                    Pay {{ $paymentConfig['admission_fee']['currency_symbol'] }}{{ number_format($paymentConfig['admission_fee']['amount']) }} via M-PESA
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Manual Payment --}}
                    @if($paymentConfig['mpesa']['manual_enabled'])
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Or Pay Manually</h3>
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <p class="text-sm text-gray-600 mb-2"><strong>Paybill:</strong> {{ $paymentConfig['mpesa']['paybill'] ?: 'N/A' }}</p>
                                <p class="text-sm text-gray-600"><strong>Account:</strong> {{ $application->application_number }}</p>
                            </div>
                            
                            <form action="{{ route('student.payment.manual', $application->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="payment_type" value="admission_fee">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">M-PESA Transaction Code</label>
                                    <input type="text" name="transaction_code" 
                                        placeholder="e.g. QHK71XXXXX"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 uppercase"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number Used</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">+254</span>
                                        <input type="tel" name="phone" 
                                            placeholder="712345678" 
                                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                            pattern="[0-9]{9}" maxlength="9" required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
                                    <input type="number" name="amount" 
                                        value="{{ $paymentConfig['admission_fee']['amount'] }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                        required>
                                </div>
                                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition-all font-semibold">
                                    Submit Manual Payment
                                </button>
                                <p class="text-xs text-gray-500 text-center">Manual payments are verified by admin within 24 hours</p>
                            </form>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Commitment Fee Section (if applicable) --}}
            @if($showCommitmentFee)
                <div class="bg-white rounded-xl shadow-sm border {{ $commitmentPayment && $commitmentPayment->status === 'completed' ? 'border-green-200' : (($commitmentPayment && $commitmentPayment->status === 'manual_pending') ? 'border-yellow-200' : 'border-blue-200') }} p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $paymentConfig['commitment_fee']['label'] }}</h2>
                            @if($commitmentDueDate)
                                <p class="text-sm text-gray-500 mt-1">
                                    Due: {{ $commitmentDueDate->format('M d, Y') }}
                                    @if($commitmentDueDate->isPast())
                                        <span class="text-red-600 font-medium">(OVERDUE)</span>
                                    @else
                                        ({{ now()->diffInDays($commitmentDueDate) }} days remaining)
                                    @endif
                                </p>
                            @endif
                        </div>
                        @if($commitmentPayment && $commitmentPayment->status === 'completed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-700">
                                <i class="fas fa-check-circle mr-1"></i> Paid
                            </span>
                        @elseif($commitmentPayment && $commitmentPayment->status === 'manual_pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock mr-1"></i> Pending Verification
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-700">
                                <i class="fas fa-exclamation-circle mr-1"></i> Required
                            </span>
                        @endif
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4">{{ $paymentConfig['commitment_fee']['description'] }}</p>
                    
                    <div class="text-2xl font-bold text-gray-900 mb-6">
                        {{ $paymentConfig['commitment_fee']['currency_symbol'] }}{{ number_format($paymentConfig['commitment_fee']['amount']) }}
                    </div>

                    @if($commitmentPayment && $commitmentPayment->status === 'completed')
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Receipt Number</p>
                                    <p class="font-semibold text-gray-900">{{ $commitmentPayment->receipt_number }}</p>
                                </div>
                                <a href="{{ route('student.payment.receipt', $commitmentPayment->id) }}" target="_blank" class="text-green-600 hover:text-green-700">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- STK Push Payment for Commitment Fee --}}
                        @if($paymentConfig['mpesa']['stk_enabled'])
                            <div class="mb-6">
                                <form action="{{ route('student.payment.initiate', $application->id) }}" method="POST" id="commitment-payment-form" class="payment-form">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="mpesa">
                                    <input type="hidden" name="payment_type" value="commitment_fee">
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">+254</span>
                                            <input type="tel" name="phone" 
                                                placeholder="712345678" 
                                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                pattern="[0-9]{9}" maxlength="9" required
                                                value="{{ auth()->user()->phone ? str_replace('254', '', auth()->user()->phone) : '' }}">
                                        </div>
                                    </div>

                                    <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-all font-semibold text-lg flex items-center justify-center gap-2 stk-push-btn">
                                        <i class="fas fa-mobile-alt"></i>
                                        Pay {{ $paymentConfig['commitment_fee']['currency_symbol'] }}{{ number_format($paymentConfig['commitment_fee']['amount']) }} via M-PESA
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Manual Payment for Commitment Fee --}}
                        @if($paymentConfig['mpesa']['manual_enabled'])
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Or Pay Manually</h3>
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <p class="text-sm text-gray-600 mb-2"><strong>Paybill:</strong> {{ $paymentConfig['mpesa']['paybill'] ?: 'N/A' }}</p>
                                    <p class="text-sm text-gray-600"><strong>Account:</strong> {{ $application->application_number }}</p>
                                </div>
                                
                                <form action="{{ route('student.payment.manual', $application->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="payment_type" value="commitment_fee">
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">M-PESA Transaction Code</label>
                                        <input type="text" name="transaction_code" 
                                            placeholder="e.g. QHK71XXXXX"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 uppercase"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number Used</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">+254</span>
                                            <input type="tel" name="phone" 
                                                placeholder="712345678" 
                                                class="w-full px-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                                pattern="[0-9]{9}" maxlength="9" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
                                        <input type="number" name="amount" 
                                            value="{{ $paymentConfig['commitment_fee']['amount'] }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                            required>
                                    </div>
                                    <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition-all font-semibold">
                                        Submit Manual Payment
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            {{-- Payment Instructions --}}
            @if($paymentConfig['payment_instructions'])
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h3 class="font-semibold text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Payment Instructions
                    </h3>
                    <p class="text-sm text-blue-700">{{ $paymentConfig['payment_instructions'] }}</p>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-4">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Payment Summary</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Application Number</span>
                        <span class="font-medium">{{ $application->application_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Program</span>
                        <span class="font-medium text-right">{{ $application->program->name ?? 'N/A' }}</span>
                    </div>
                    <hr class="border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ $paymentConfig['admission_fee']['label'] }}</span>
                        <span class="font-medium">{{ $paymentConfig['admission_fee']['currency_symbol'] }}{{ number_format($paymentConfig['admission_fee']['amount']) }}</span>
                    </div>
                    @if($showCommitmentFee)
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ $paymentConfig['commitment_fee']['label'] }}</span>
                            <span class="font-medium">{{ $paymentConfig['commitment_fee']['currency_symbol'] }}{{ number_format($paymentConfig['commitment_fee']['amount']) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg">
                            <span class="font-semibold">Total Due</span>
                            <span class="font-bold text-[#00008B]">{{ $paymentConfig['admission_fee']['currency_symbol'] }}{{ number_format($paymentConfig['admission_fee']['amount'] + $paymentConfig['commitment_fee']['amount']) }}</span>
                        </div>
                    @else
                        <div class="flex justify-between text-lg">
                            <span class="font-semibold">Total</span>
                            <span class="font-bold text-[#00008B]">{{ $paymentConfig['admission_fee']['currency_symbol'] }}{{ number_format($paymentConfig['admission_fee']['amount']) }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">How to Pay via M-PESA</h4>
                    <ol class="text-xs text-gray-600 space-y-1">
                        <li>1. Click "Pay via M-PESA"</li>
                        <li>2. Enter your phone number</li>
                        <li>3. Wait for M-PESA prompt</li>
                        <li>4. Enter your M-PESA PIN</li>
                        <li>5. Confirm on this page</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History --}}
    @if($payments->count() > 0)
        <div class="mt-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Payment History</h3>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($payments as $payment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $payment->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ ucwords(str_replace('_', ' ', $payment->payment_type ?? 'Fee')) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 capitalize">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    KES {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $payment->getStatusBadgeClass() }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $payment->receipt_number ?? ($payment->mpesa_receipt ?? '-') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@if($pendingPayment)
<div id="status-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-md mx-4">
        <div id="status-content" class="text-center">
            <div class="animate-spin w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-gray-600">Checking payment status...</p>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
let paymentStartTime = null;
let statusCheckInterval = null;

document.addEventListener('DOMContentLoaded', function() {
    // Phone number formatting
    document.querySelectorAll('input[type="tel"][name="phone"]').forEach(input => {
        input.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
    });

    // Transaction code uppercase
    document.querySelectorAll('input[name="transaction_code"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    });

    // STK Push form submission
    document.querySelectorAll('.payment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const phoneInput = this.querySelector('input[name="phone"]');
            const phone = phoneInput.value;
            
            if (phone.length !== 9) {
                e.preventDefault();
                alert('Please enter a valid 9-digit phone number');
                return;
            }

            const btn = this.querySelector('.stk-push-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending payment request...';
        });
    });

    // Initialize payment status polling if waiting
    @if(session('waiting_payment'))
        paymentStartTime = Date.now();
        startPaymentPolling();
    @endif
});

function startPaymentPolling() {
    if (statusCheckInterval) return;
    
    statusCheckInterval = setInterval(checkPaymentStatus, 5000);
    
    // Stop after 2 minutes
    setTimeout(() => {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
            updateStatusMessage('Payment timeout. Please try again or use manual payment.', true);
        }
    }, 120000);
}

@if($pendingPayment)
function checkPaymentStatus() {
    const modal = document.getElementById('status-modal');
    const content = document.getElementById('status-content');
    
    if(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    fetch('{{ route('student.payment.check-status', $application->id) }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'completed') {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
                
                if(content) {
                    content.innerHTML = `
                        <div class="text-green-500 text-6xl mb-4"><i class="fas fa-check-circle"></i></div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                        <p class="text-gray-600">Receipt: ${data.receipt_number || 'N/A'}</p>
                        <a href="{{ route('student.dashboard') }}" class="inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                            Return to Dashboard
                        </a>
                    `;
                }
                setTimeout(() => location.reload(), 3000);
            } else if (data.status === 'failed') {
                if(content) {
                    content.innerHTML = `
                        <div class="text-red-500 text-6xl mb-4"><i class="fas fa-times-circle"></i></div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Payment Failed</h3>
                        <p class="text-gray-600">${data.message}</p>
                        <button onclick="closeModal()" class="mt-4 bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                            Try Again
                        </button>
                    `;
                }
            } else {
                if(content) {
                    const elapsed = Math.floor((Date.now() - paymentStartTime) / 1000);
                    content.innerHTML = `
                        <div class="text-yellow-500 text-6xl mb-4"><i class="fas fa-clock"></i></div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Still Processing</h3>
                        <p class="text-gray-600">${data.message}</p>
                        <p class="text-sm text-gray-400 mt-2">Time elapsed: ${elapsed}s</p>
                        <button onclick="checkPaymentStatus()" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Check Again
                        </button>
                    `;
                }
            }
        })
        .catch(error => {
            if(content) {
                content.innerHTML = `
                    <div class="text-red-500 text-6xl mb-4"><i class="fas fa-exclamation-triangle"></i></div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Error</h3>
                    <p class="text-gray-600">Failed to check payment status</p>
                    <button onclick="closeModal()" class="mt-4 bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                        Close
                    </button>
                `;
            }
        });
}
@endif

function closeModal() {
    const modal = document.getElementById('status-modal');
    if(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function updateStatusMessage(message, isError = false) {
    const statusContainer = document.getElementById('payment-status-container');
    if (statusContainer) {
        const statusMessage = document.getElementById('payment-status-message');
        if (statusMessage) {
            statusMessage.textContent = message;
            statusMessage.className = isError ? 'text-sm text-red-600' : 'text-sm text-gray-600';
        }
    }
}
</script>
@endpush