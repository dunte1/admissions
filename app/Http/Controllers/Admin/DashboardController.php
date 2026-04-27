<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Application;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Student;
use App\Models\Program;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected const CACHE_TTL = 300;

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $user = auth()->user();
        
        if (!$user->can('view_applications') && !$user->can('view_payments') && !$user->can('view_reports')) {
            abort(403, 'You do not have permission to access this page.');
        }
        
        $school = current_school();
        $cacheKey = "dashboard_stats_school_{$school->id}";
        
        $stats = Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->getDashboardStats();
        });
        
        $recentApplications = Application::with(['student', 'program', 'user'])
            ->latest()
            ->take(10)
            ->get();
        
        $applicationsByProgram = Program::withCount('applications')
            ->where('is_active', true)
            ->orderByDesc('applications_count')
            ->take(5)
            ->get();
        
        $recentActivities = AuditLog::with('user')
            ->latest()
            ->take(5)
            ->get();

        $chartData = Cache::remember("chart_data_school_{$school->id}", self::CACHE_TTL, function () {
            return $this->getChartData();
        });
        
        $revenueStats = Cache::remember("revenue_stats_school_{$school->id}", self::CACHE_TTL, function () {
            return $this->getRevenueStats();
        });

        return view('admin.dashboard', compact(
            'stats',
            'recentApplications',
            'applicationsByProgram',
            'recentActivities',
            'chartData',
            'revenueStats',
            'school'
        ));
    }

    protected function getDashboardStats(): array
    {
        $schoolId = current_school()->id;
        
        $total = Application::forSchool($schoolId)->count();
        $pending = Application::forSchool($schoolId)->where('status', 'pending')->count();
        $approved = Application::forSchool($schoolId)->where('status', 'approved')->count();
        $rejected = Application::forSchool($schoolId)->where('status', 'rejected')->count();
        $underReview = Application::forSchool($schoolId)->where('status', 'under_review')->count();
        $infoRequested = Application::forSchool($schoolId)->where('status', 'info_requested')->count();

        $today = now()->startOfDay();
        $todayApplications = Application::forSchool($schoolId)->whereDate('created_at', $today)->count();
        $thisWeek = Application::forSchool($schoolId)->whereDate('created_at', '>=', now()->startOfWeek())->count();
        $thisMonth = Application::forSchool($schoolId)->whereMonth('created_at', now()->month)->count();

        $lastMonthTotal = Application::forSchool($schoolId)->whereMonth('created_at', now()->subMonth()->month)->count();
        $monthlyTrend = $lastMonthTotal > 0 ? round((($thisMonth - $lastMonthTotal) / $lastMonthTotal) * 100, 1) : ($thisMonth > 0 ? 100 : 0);

        $lastWeekTotal = Application::forSchool($schoolId)->whereDate('created_at', '>=', now()->subWeek()->startOfWeek())->count();
        $weeklyTrend = $lastWeekTotal > 0 ? round((($thisWeek - $lastWeekTotal) / $lastWeekTotal) * 100, 1) : ($thisWeek > 0 ? 100 : 0);

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'under_review' => $underReview,
            'info_requested' => $infoRequested,
            'today' => $todayApplications,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
            'monthly_trend' => $monthlyTrend,
            'weekly_trend' => $weeklyTrend,
            'conversion_rate' => $total > 0 ? round(($approved / $total) * 100, 1) : 0,
        ];
    }

    protected function getChartData(): array
    {
        $schoolId = current_school()->id;
        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "strftime('%m', created_at)" : 'MONTH(created_at)';

        $monthlyApplications = Application::forSchool($schoolId)
            ->selectRaw("{$monthExpr} as month, COUNT(*) as count")
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = $monthlyApplications[$i] ?? 0;
        }

        $statusDistribution = Application::forSchool($schoolId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusLabels = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'under_review' => 'Under Review',
            'info_requested' => 'Info Requested',
            'draft' => 'Draft',
        ];

        $programApplications = Program::withCount('applications')
            ->where('is_active', true)
            ->orderByDesc('applications_count')
            ->take(6)
            ->get();

        return [
            'monthly' => $monthlyData,
            'status_distribution' => $statusDistribution,
            'status_labels' => $statusLabels,
            'programs' => $programApplications->pluck('name')->toArray(),
            'program_counts' => $programApplications->pluck('applications_count')->toArray(),
        ];
    }

    protected function getRevenueStats(): array
    {
        $schoolId = current_school()->id;
        
        $totalRevenue = Payment::forSchool($schoolId)->where('status', 'completed')->sum('amount');
        $pendingPayments = Payment::forSchool($schoolId)->where('status', 'pending')->sum('amount');
        $todayRevenue = Payment::forSchool($schoolId)
            ->whereDate('paid_at', now()->today())
            ->where('status', 'completed')
            ->sum('amount');
        $monthRevenue = Payment::forSchool($schoolId)
            ->whereMonth('paid_at', now()->month)
            ->where('status', 'completed')
            ->sum('amount');

        return [
            'total' => $totalRevenue,
            'pending' => $pendingPayments,
            'today' => $todayRevenue,
            'month' => $monthRevenue,
        ];
    }

    public function applications(Request $request)
    {
        $query = Application::with(['student', 'program', 'user', 'payments', 'documents']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('application_number', 'like', "%{$request->search}%")
                    ->orWhereHas('student', function ($sq) use ($request) {
                        $sq->where('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%")
                            ->orWhere('id_number', 'like', "%{$request->search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($request) {
                        $uq->where('email', 'like', "%{$request->search}%")
                            ->orWhere('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('payment_status')) {
            $query->whereHas('payments', function ($q) use ($request) {
                if ($request->payment_status === 'paid') {
                    $q->where('status', 'completed');
                } elseif ($request->payment_status === 'pending') {
                    $q->where('status', 'pending');
                } elseif ($request->payment_status === 'unpaid') {
                    $q->doesntHave('payments');
                }
            });
        }

        if ($request->sort_by) {
            $sortBy = $request->sort_by;
            $sortDir = $request->sort_dir ?? 'desc';
            
            if ($sortBy === 'student_name') {
                $query->leftJoin('students', 'applications.student_id', '=', 'students.id')
                    ->orderBy('students.first_name', $sortDir)
                    ->orderBy('students.last_name', $sortDir)
                    ->select('applications.*');
            } else {
                $query->orderBy($sortBy, $sortDir);
            }
        } else {
            $query->latest();
        }

        $applications = $query->paginate(20)->withQueryString();
        $programs = Program::where('is_active', true)->get();

        return view('admin.applications.index', compact('applications', 'programs'));
    }

    public function showApplication(Application $application)
    {
        $application->load(['student', 'program', 'user', 'documents', 'payments', 'reviewer']);
        
        $applicationData = is_array($application->form_data) ? $application->form_data : [];
        
        $timeline = $this->buildApplicationTimeline($application);
        
        return view('admin.applications.show', compact('application', 'applicationData', 'timeline'));
    }

    protected function buildApplicationTimeline(Application $application): array
    {
        $timeline = [];
        
        $timeline[] = [
            'title' => 'Application Submitted',
            'description' => 'Application received',
            'date' => $application->created_at,
            'icon' => 'document',
            'color' => 'blue',
            'completed' => true,
        ];

        if ($application->submitted_at) {
            $timeline[] = [
                'title' => 'Under Review',
                'description' => 'Application is being reviewed',
                'date' => $application->submitted_at,
                'icon' => 'search',
                'color' => 'yellow',
                'completed' => true,
            ];
        }

        if ($application->status === 'approved') {
            $timeline[] = [
                'title' => 'Approved',
                'description' => 'Application approved',
                'date' => $application->reviewed_at,
                'icon' => 'check',
                'color' => 'green',
                'completed' => true,
            ];
        } elseif ($application->status === 'rejected') {
            $timeline[] = [
                'title' => 'Rejected',
                'description' => $application->review_notes ?? 'Application not successful',
                'date' => $application->reviewed_at,
                'icon' => 'x',
                'color' => 'red',
                'completed' => true,
            ];
        } elseif ($application->status === 'info_requested') {
            $timeline[] = [
                'title' => 'Additional Info Requested',
                'description' => $application->review_notes ?? 'Please provide additional information',
                'date' => $application->updated_at,
                'icon' => 'question',
                'color' => 'orange',
                'completed' => true,
            ];
        }

        foreach ($application->payments as $payment) {
            if ($payment->status === 'completed') {
                $timeline[] = [
                    'title' => 'Payment Received',
                    'description' => 'KES ' . number_format($payment->amount),
                    'date' => $payment->paid_at,
                    'icon' => 'currency',
                    'color' => 'green',
                    'completed' => true,
                ];
            }
        }

        return collect($timeline)->sortBy('date')->values()->toArray();
    }

    public function approve(Request $request, Application $application)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($application, $request) {
            $application->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $request->notes,
            ]);

            AuditLog::log('approve_application', $application, null, ['status' => 'approved']);
        });

        $application->user->notify(new \App\Notifications\ApplicationApproved($application));

        return redirect()->back()->with('success', __('labels.application') . ' approved successfully!');
    }

    public function reject(Request $request, Application $application)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($application, $request) {
            $application->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $request->notes,
            ]);

            AuditLog::log('reject_application', $application, null, ['status' => 'rejected']);
        });

        $application->user->notify(new \App\Notifications\ApplicationRejected($application));

        return redirect()->back()->with('success', __('labels.application') . ' rejected.');
    }

    public function requestInfo(Request $request, Application $application)
    {
        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $application->update([
            'status' => 'info_requested',
            'review_notes' => $request->notes,
        ]);

        AuditLog::log('request_info', $application, null, ['status' => 'info_requested']);

        $application->user->notify(new \App\Notifications\InfoRequested($application));

        return redirect()->back()->with('success', 'Information requested from applicant.');
    }

    public function export(Request $request)
    {
        $query = Application::with(['student', 'program']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $applications = $query->get();

        if ($request->format === 'pdf') {
            return $this->exportPdf($applications);
        }

        return $this->exportExcel($applications);
    }

    protected function exportPdf($applications)
    {
        $pdf = \PDF::loadView('admin.exports.applications-pdf', compact('applications'));
        return $pdf->download('applications-' . date('Y-m-d') . '.pdf');
    }

    public function exportSinglePdf(Application $application)
    {
        $application->load(['student', 'program', 'user', 'documents', 'payments']);
        $formData = is_array($application->form_data) ? $application->form_data : [];
        
        $pdf = \PDF::loadView('admin.exports.application-single-pdf', compact('application', 'formData'));
        $pdf->setPaper('A4');
        
        return $pdf->download('application-' . $application->application_number . '.pdf');
    }

    protected function exportExcel($applications)
    {
        return \Excel::download(new \App\Exports\ApplicationsExport($applications), 'applications-' . date('Y-m-d') . '.xlsx');
    }

    public function analytics()
    {
        $driver = DB::connection()->getDriverName();
        $yearExpr = $driver === 'sqlite' ? "strftime('%Y', created_at)" : 'YEAR(created_at)';
        $monthExpr = $driver === 'sqlite' ? "strftime('%m', created_at)" : 'MONTH(created_at)';

        $monthlyStats = Application::selectRaw("{$monthExpr} as month, {$yearExpr} as year, COUNT(*) as total")
            ->whereRaw("{$yearExpr} = ?", [date('Y')])
            ->groupBy('month', 'year')
            ->get();

        $statusDistribution = Application::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $topPrograms = Program::withCount(['applications' => function ($query) {
            $query->where('status', 'approved');
        }])->orderByDesc('applications_count')->take(10)->get();

        $monthlyApplications = Application::selectRaw("{$monthExpr} as month, COUNT(*) as count")
            ->whereRaw("{$yearExpr} = ?", [now()->year])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyApplications[$i] ?? 0;
        }

        return view('admin.analytics', compact('monthlyStats', 'statusDistribution', 'topPrograms', 'chartData'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'exists:applications,id',
            'action' => 'required|in:approve,reject,request_info,delete',
            'notes' => 'nullable|string|max:1000',
        ]);

        $applicationIds = $request->application_ids;
        $action = $request->action;
        $notes = $request->notes;
        $processed = 0;

        $applications = Application::whereIn('id', $applicationIds)->get();

        foreach ($applications as $application) {
            switch ($action) {
                case 'approve':
                    $application->update([
                        'status' => 'approved',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                        'review_notes' => $notes,
                    ]);
                    AuditLog::log('bulk_approve', $application, null, ['bulk' => true]);
                    $application->user->notify(new \App\Notifications\ApplicationApproved($application));
                    break;

                case 'reject':
                    $application->update([
                        'status' => 'rejected',
                        'reviewed_by' => auth()->id(),
                        'reviewed_at' => now(),
                        'review_notes' => $notes,
                    ]);
                    AuditLog::log('bulk_reject', $application, null, ['bulk' => true]);
                    $application->user->notify(new \App\Notifications\ApplicationRejected($application));
                    break;

                case 'request_info':
                    $application->update([
                        'status' => 'info_requested',
                        'review_notes' => $notes,
                    ]);
                    AuditLog::log('bulk_request_info', $application, null, ['bulk' => true]);
                    $application->user->notify(new \App\Notifications\InfoRequested($application));
                    break;

                case 'delete':
                    AuditLog::log('bulk_delete', $application, null, ['bulk' => true]);
                    $application->delete();
                    break;
            }
            $processed++;
        }

        $actionLabels = [
            'approve' => 'approved',
            'reject' => 'rejected',
            'request_info' => 'requested info for',
            'delete' => 'deleted',
        ];

        return redirect()->back()->with('success', "{$processed} applications {$actionLabels[$action]} successfully!");
    }
}
