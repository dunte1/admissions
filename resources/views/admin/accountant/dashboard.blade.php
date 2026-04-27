@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">
            @if($school)
                {{ $school->name }} - Finance Dashboard
            @else
                Finance Dashboard
            @endif
        </h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.accountant.payments') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-list mr-2"></i> All Payments
            </a>
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Payments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['pending_amount'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['today_revenue'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['month_revenue'], 2) }}</p>
                    <p class="text-xs {{ $stats['month_trend'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stats['month_trend'] >= 0 ? '+' : '' }}{{ $stats['month_trend'] }}% vs last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Revenue Overview (Last 30 Days)</h3>
            </div>
            <div class="p-6">
                <canvas id="revenueChart" height="200"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Payment Methods</h3>
            </div>
            <div class="p-6 space-y-4">
                @forelse($paymentMethods as $method => $data)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-credit-card text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $method)) }}</p>
                                <p class="text-sm text-gray-500">{{ $data['count'] }} transactions</p>
                            </div>
                        </div>
                        <p class="font-semibold text-gray-900">{{ number_format($data['total'], 2) }}</p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4">No payment data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                <a href="{{ route('admin.accountant.payments') }}" class="text-purple-600 hover:text-purple-700 text-sm">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentPayments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 text-sm">{{ $payment->transaction_id ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->application?->student)
                                        <p class="font-medium text-gray-900 text-sm">{{ $payment->application->student->full_name }}</p>
                                        <p class="text-xs text-gray-500">#{{ $payment->application->application_number }}</p>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ number_format($payment->amount, 2) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($payment->status === 'completed') bg-green-100 text-green-800
                                        @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No recent payments</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pending Payments</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pendingPayments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 text-sm">{{ $payment->transaction_id ?? 'N/A' }}</p>
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
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $payment->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No pending payments</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyRevenue['labels'] ?? []) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($dailyRevenue['values'] ?? []) !!},
                borderColor: '#7C3AED',
                backgroundColor: 'rgba(124, 58, 237, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KES ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
