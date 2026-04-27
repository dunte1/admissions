<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountantDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:accountant']);
    }

    public function index()
    {
        $school = current_school();
        $stats = $this->getPaymentStats();
        $recentPayments = $this->getRecentPayments();
        $paymentMethods = $this->getPaymentMethodStats();
        $dailyRevenue = $this->getDailyRevenue();
        $pendingPayments = $this->getPendingPayments();

        return view('admin.accountant.dashboard', compact(
            'stats',
            'recentPayments',
            'paymentMethods',
            'dailyRevenue',
            'pendingPayments',
            'school'
        ));
    }

    protected function getPaymentStats(): array
    {
        $completedPayments = Payment::where('status', 'completed');
        $pendingPayments = Payment::where('status', 'pending');

        $totalRevenue = (clone $completedPayments)->sum('amount');
        $pendingAmount = (clone $pendingPayments)->sum('amount');
        
        $todayRevenue = Payment::whereDate('paid_at', now()->today())
            ->where('status', 'completed')
            ->sum('amount');
        
        $weekRevenue = Payment::whereDate('paid_at', '>=', now()->startOfWeek())
            ->where('status', 'completed')
            ->sum('amount');
        
        $monthRevenue = Payment::whereMonth('paid_at', now()->month)
            ->where('status', 'completed')
            ->sum('amount');

        $lastMonthRevenue = Payment::whereMonth('paid_at', now()->subMonth()->month)
            ->where('status', 'completed')
            ->sum('amount');

        $monthTrend = $lastMonthRevenue > 0 
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) 
            : ($monthRevenue > 0 ? 100 : 0);

        return [
            'total_revenue' => $totalRevenue,
            'pending_amount' => $pendingAmount,
            'today_revenue' => $todayRevenue,
            'week_revenue' => $weekRevenue,
            'month_revenue' => $monthRevenue,
            'month_trend' => $monthTrend,
            'completed_count' => (clone $completedPayments)->count(),
            'pending_count' => (clone $pendingPayments)->count(),
        ];
    }

    protected function getRecentPayments(int $limit = 10)
    {
        return Payment::with(['application.student', 'user'])
            ->latest()
            ->take($limit)
            ->get();
    }

    protected function getPaymentMethodStats(): array
    {
        return Payment::where('status', 'completed')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn($item) => [$item->payment_method => [
                'count' => $item->count,
                'total' => $item->total,
            ]]);
    }

    protected function getDailyRevenue(int $days = 30): array
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $amount = Payment::whereDate('paid_at', $date)
                ->where('status', 'completed')
                ->sum('amount');
            $data['labels'][] = Carbon::parse($date)->format('M d');
            $data['values'][] = $amount;
        }
        return $data;
    }

    protected function getPendingPayments(int $limit = 10)
    {
        return Payment::with(['application.student', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['application.student', 'user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'like', "%{$request->search}%")
                    ->orWhereHas('application.student', function ($sq) use ($request) {
                        $sq->where('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%")
                            ->orWhere('id_number', 'like', "%{$request->search}%");
                    })
                    ->orWhereHas('application', function ($aq) use ($request) {
                        $aq->where('application_number', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->date_from) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(25)->withQueryString();

        return view('admin.accountant.payments', compact('payments'));
    }

    public function export(Request $request)
    {
        $query = Payment::with(['application.student']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        $payments = $query->get();

        return \Excel::download(
            new \App\Exports\PaymentsExport($payments), 
            'payments-' . date('Y-m-d') . '.xlsx'
        );
    }
}
