<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Application;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Models\Intake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $stats = $this->getSystemStats();
        $recentApplications = $this->getRecentApplications();
        $recentSchools = $this->getRecentSchools();
        $applicationStats = $this->getApplicationStats();
        $revenueStats = $this->getRevenueStats();
        $schoolsByStatus = $this->getSchoolsByStatus();
        $topSchools = $this->getTopSchools();

        return view('super-admin.dashboard', compact(
            'stats',
            'recentApplications',
            'recentSchools',
            'applicationStats',
            'revenueStats',
            'schoolsByStatus',
            'topSchools'
        ));
    }

    protected function getSystemStats(): array
    {
        return [
            'total_schools' => School::count(),
            'active_schools' => School::where('status', 'active')->count(),
            'total_students' => Student::withoutGlobalScopes()->count(),
            'total_applications' => Application::withoutGlobalScopes()->count(),
            'total_programs' => Program::withoutGlobalScopes()->count(),
            'total_users' => User::withoutGlobalScopes()->count(),
            'total_revenue' => Payment::withoutGlobalScopes()->where('status', 'completed')->sum('amount'),
            'pending_applications' => Application::withoutGlobalScopes()->where('status', 'pending')->count(),
            'approved_applications' => Application::withoutGlobalScopes()->where('status', 'approved')->count(),
            'rejected_applications' => Application::withoutGlobalScopes()->where('status', 'rejected')->count(),
        ];
    }

    protected function getRecentApplications(int $limit = 10)
    {
        return Application::withoutGlobalScopes()
            ->with(['user', 'program', 'school'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getRecentSchools(int $limit = 5)
    {
        return School::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'code', 'status', 'logo', 'created_at']);
    }

    protected function getApplicationStats(): array
    {
        $months = now()->subMonths(5)->monthsUntil(now());
        $data = [];
        
        foreach ($months as $month) {
            $count = Application::withoutGlobalScopes()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $data['labels'][] = $month->format('M Y');
            $data['values'][] = $count;
        }
        
        return $data;
    }

    protected function getRevenueStats(): array
    {
        $months = now()->subMonths(5)->monthsUntil(now());
        $data = [];
        
        foreach ($months as $month) {
            $total = Payment::withoutGlobalScopes()
                ->where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $data['labels'][] = $month->format('M Y');
            $data['values'][] = (float) $total;
        }
        
        return $data;
    }

    protected function getSchoolsByStatus(): array
    {
        return [
            'labels' => ['Active', 'Inactive', 'Suspended'],
            'values' => [
                School::where('status', 'active')->count(),
                School::where('status', 'inactive')->count(),
                School::where('status', 'suspended')->count(),
            ],
        ];
    }

    protected function getTopSchools(int $limit = 5)
    {
        return School::withCount([
            'applications as applications_count',
            'users as users_count',
        ])
        ->withSum([
            'payments as total_revenue' => fn($q) => $q->where('status', 'completed')
        ], 'amount')
        ->orderByDesc('applications_count')
        ->limit($limit)
        ->get(['id', 'name', 'logo']);
    }

    public function activityLogs(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');
        
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->paginate(50);
        
        $actions = AuditLog::distinct()->pluck('action');
        
        return view('super-admin.activity-logs', compact('logs', 'actions'));
    }

    public function systemHealth()
    {
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();
        
        $diskUsage = [
            'total' => disk_total_space(base_path()),
            'free' => disk_free_space(base_path()),
            'used_percent' => round((disk_total_space(base_path()) - disk_free_space(base_path())) / disk_total_space(base_path()) * 100, 2),
        ];
        
        $memoryUsage = [
            'used' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
        ];
        
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        } else {
            $tables = DB::select('SHOW TABLES');
        }
        
        $dbStats = [
            'tables' => count($tables),
        ];
        
        $errorLogs = $this->getRecentErrors();
        $systemInfo = [
            'php_version' => $phpVersion,
            'laravel_version' => $laravelVersion,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.default'),
        ];
        
        return view('super-admin.system-health', compact(
            'diskUsage',
            'memoryUsage',
            'dbStats',
            'errorLogs',
            'systemInfo'
        ));
    }

    protected function getRecentErrors(): array
    {
        $logFile = storage_path('logs/laravel.log');
        $errors = [];
        
        if (file_exists($logFile)) {
            $lines = file($logFile);
            $errors = array_slice($lines, -100);
        }
        
        return $errors;
    }

    public function broadcastNotification(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'schools' => 'nullable|string',
            ]);
            
            $schools = $request->schools === 'all' 
                ? School::where('status', 'active')->get() 
                : School::whereIn('id', explode(',', $request->schools))->get();
            
            foreach ($schools as $school) {
                $users = $school->users;
                foreach ($users as $user) {
                    $user->notify(new \App\Notifications\SystemBroadcast($request->title, $request->message));
                }
            }
            
            return redirect()->back()->with('success', 'Notification sent to ' . $schools->count() . ' schools.');
        }
        
        return view('super-admin.broadcast');
    }

    public function exportReport(Request $request, string $type)
    {
        $data = match($type) {
            'schools' => $this->exportSchoolsReport(),
            'applications' => $this->exportApplicationsReport($request),
            'revenue' => $this->exportRevenueReport($request),
            default => abort(404),
        };
        
        return $data;
    }

    protected function exportSchoolsReport()
    {
        $schools = School::withCount(['users', 'applications', 'programs'])
            ->withSum(['payments' => fn($q) => $q->where('status', 'completed')], 'amount')
            ->get();
        
        $csv = "Name,Code,Status,Users,Applications,Programs,Revenue\n";
        foreach ($schools as $school) {
            $csv .= "{$school->name},{$school->code},{$school->status},{$school->users_count},{$school->applications_count},{$school->programs_count},{$school->payments_sum_amount}\n";
        }
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="schools-report-' . date('Y-m-d') . '.csv"',
        ]);
    }

    protected function exportApplicationsReport(Request $request)
    {
        $query = Application::withoutGlobalScopes()->with(['user', 'school', 'program']);
        
        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $applications = $query->get();
        
        $csv = "Application Number,Student,School,Program,Status,Applied Date\n";
        foreach ($applications as $app) {
            $csv .= "{$app->application_number},{$app->user->fullName()},{$app->school->name},{$app->program->name},{$app->status},{$app->created_at->format('Y-m-d')}\n";
        }
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="applications-report-' . date('Y-m-d') . '.csv"',
        ]);
    }

    protected function exportRevenueReport(Request $request)
    {
        $query = Payment::withoutGlobalScopes()->with('school')->where('status', 'completed');
        
        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $payments = $query->get();
        
        $csv = "Reference,School,Amount,Currency,Method,Date\n";
        foreach ($payments as $payment) {
            $csv .= "{$payment->reference},{$payment->school->name},{$payment->amount},{$payment->currency},{$payment->payment_method},{$payment->created_at->format('Y-m-d H:i')}\n";
        }
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="revenue-report-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
