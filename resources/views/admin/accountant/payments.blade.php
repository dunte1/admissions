@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Payment Management</h1>
        <div class="flex items-center space-x-3">
            <form action="{{ route('admin.accountant.export') }}" method="GET" class="inline">
                <button type="submit" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Pending Manual Verifications --}}
    @php
        $pendingManualPayments = $payments->filter(fn($p) => $p->status === 'manual_pending');
    @endphp
    @if($pendingManualPayments->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>Pending Manual Verifications ({{ $pendingManualPayments->count() }})
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($pendingManualPayments as $payment)
            <div class="bg-white rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-gray-900">{{ $payment->application?->application_number }}</span>
                    <span class="text-sm text-gray-500">{{ $payment->created_at->format('M d') }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">{{ $payment->application?->student?->full_name ?? 'N/A' }}</p>
                <p class="font-bold text-gray-900">KES {{ number_format($payment->amount) }}</p>
                <p class="text-xs text-gray-500 mb-3">Code: {{ $payment->transaction_code ?? $payment->transaction_id }}</p>
                <div class="flex gap-2">
                    <button onclick="verifyPayment({{ $payment->id }})" class="flex-1 bg-green-600 text-white py-2 rounded-lg text-sm hover:bg-green-700">
                        Verify
                    </button>
                    <button onclick="rejectPayment({{ $payment->id }})" class="flex-1 bg-red-600 text-white py-2 rounded-lg text-sm hover:bg-red-700">
                        Reject
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="manual_pending" {{ request('status') === 'manual_pending' ? 'selected' : '' }}>Manual Pending</option>
                    </select>
                </div>
                <div>
                    <select name="payment_method" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Methods</option>
                        <option value="mpesa" {{ request('payment_method') === 'mpesa' ? 'selected' : '' }}>MPESA</option>
                        <option value="paypal" {{ request('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="manual_mpesa" {{ request('payment_method') === 'manual_mpesa' ? 'selected' : '' }}>Manual M-PESA</option>
                        <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div class="md:col-span-5 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.accountant.payments') }}" class="ml-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900 text-sm">{{ $payment->transaction_id ?? 'N/A' }}</p>
                                @if($payment->mpesa_receipt)
                                    <p class="text-xs text-gray-500">Receipt: {{ $payment->mpesa_receipt }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->application)
                                    <a href="{{ route('admin.applications.show', $payment->application) }}" class="text-purple-600 hover:text-purple-700">
                                        {{ $payment->application->application_number }}
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->application?->student)
                                    <p class="font-medium text-gray-900 text-sm">{{ $payment->application->student->full_name }}</p>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ number_format($payment->amount, 2) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700">
                                    @switch($payment->payment_method)
                                        @case('mpesa')
                                            <i class="fas fa-mobile-alt text-green-600 mr-1"></i>
                                            @break
                                        @case('paypal')
                                            <i class="fab fa-paypal text-blue-600 mr-1"></i>
                                            @break
                                        @case('manual_mpesa')
                                            <i class="fas fa-hand-paper text-yellow-600 mr-1"></i>
                                            @break
                                        @case('bank_transfer')
                                            <i class="fas fa-university text-gray-600 mr-1"></i>
                                            @break
                                    @endswitch
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'N/A')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($payment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($payment->status === 'manual_pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <p>{{ $payment->paid_at?->format('M d, Y') ?? 'N/A' }}</p>
                                <p class="text-xs">{{ $payment->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($payment->status === 'completed' && $payment->receipt_number)
                                <a href="{{ route('admin.payments.receipt', $payment->id) }}" target="_blank" class="text-purple-600 hover:text-purple-700 inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Receipt
                                </a>
                                @elseif($payment->status === 'manual_pending')
                                <div class="flex gap-1">
                                    <button onclick="verifyPayment({{ $payment->id }})" class="text-green-600 hover:text-green-700" title="Verify">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectPayment({{ $payment->id }})" class="text-red-600 hover:text-red-700" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($payments as $payment)
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $payment->transaction_id ?? 'N/A' }}</p>
                        @if($payment->mpesa_receipt)<p class="text-xs text-gray-500">Receipt: {{ $payment->mpesa_receipt }}</p>@endif
                    </div>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                        @if($payment->status === 'completed') bg-green-100 text-green-800
                        @elseif($payment->status === 'pending' || $payment->status === 'manual_pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="font-semibold text-gray-900">{{ number_format($payment->amount, 2) }}</span>
                    <span class="text-gray-600">
                        @switch($payment->payment_method)
                            @case('mpesa')<i class="fas fa-mobile-alt text-green-600 mr-1"></i>@break
                            @case('paypal')<i class="fab fa-paypal text-blue-600 mr-1"></i>@break
                            @case('manual_mpesa')<i class="fas fa-hand-paper text-yellow-600 mr-1"></i>@break
                            @case('bank_transfer')<i class="fas fa-university text-gray-600 mr-1"></i>@break
                        @endswitch
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'N/A')) }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                    <span>{{ $payment->application?->student?->full_name ?? 'N/A' }}</span>
                    <span>{{ $payment->paid_at?->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                @if($payment->application)
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('admin.applications.show', $payment->application) }}" class="flex-1 block py-2 text-center text-sm text-purple-600 hover:bg-purple-50 rounded-lg">
                        <i class="fas fa-eye mr-1"></i> View Application
                    </a>
                    @if($payment->status === 'completed' && $payment->receipt_number)
                    <a href="{{ route('admin.payments.receipt', $payment->id) }}" target="_blank" class="flex-1 block py-2 text-center text-sm text-green-600 hover:bg-green-50 rounded-lg">
                        <i class="fas fa-receipt mr-1"></i> Receipt
                    </a>
                    @endif
                </div>
                @endif
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <p class="text-sm">No payments found</p>
            </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $payments->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- Verification Modals --}}
<div id="verify-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeModals()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Verify Payment</h3>
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <p class="text-sm"><strong>Transaction:</strong> <span id="modal-transaction"></span></p>
            <p class="text-sm"><strong>Amount:</strong> KES <span id="modal-amount"></span></p>
        </div>
        <form id="verify-form" method="POST">
            @csrf
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModals()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Verify</button>
            </div>
        </form>
    </div>
</div>

<div id="reject-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeModals()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Payment</h3>
        <form id="reject-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <textarea name="reason" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" required placeholder="Enter rejection reason..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModals()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function verifyPayment(id) {
    document.getElementById('verify-form').action = `/admin/payments/${id}/verify`;
    document.getElementById('verify-modal').classList.remove('hidden');
}

function rejectPayment(id) {
    document.getElementById('reject-form').action = `/admin/payments/${id}/reject`;
    document.getElementById('reject-modal').classList.remove('hidden');
}

function closeModals() {
    document.getElementById('verify-modal').classList.add('hidden');
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endpush
