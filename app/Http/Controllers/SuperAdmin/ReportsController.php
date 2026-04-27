<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Application;
use App\Models\Program;
use App\Models\Payment;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:super_admin']);
    }

    public function index()
    {
        return view('super-admin.reports.index');
    }

    public function applications(Request $request)
    {
        $schools = School::active()->orderBy('name')->get();

        $query = Application::with(['school', 'program', 'intake'])
            ->when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $applications = $query->orderBy('created_at', 'desc')->paginate(25);

        $stats = [
            'total' => Application::when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))->count(),
            'pending' => Application::when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))->where('status', 'pending')->count(),
            'approved' => Application::when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))->where('status', 'approved')->count(),
            'rejected' => Application::when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))->where('status', 'rejected')->count(),
            'conversion_rate' => 0,
        ];

        if ($stats['total'] > 0) {
            $stats['conversion_rate'] = round(($stats['approved'] / $stats['total']) * 100, 1);
        }

        return view('super-admin.reports.applications', compact('applications', 'schools', 'stats'));
    }

    public function schools(Request $request)
    {
        $query = School::withCount(['applications', 'users', 'programs', 'students']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $schools = $query->orderBy('name')->paginate(25);

        $stats = [
            'total' => School::count(),
            'active' => School::where('status', 'active')->count(),
            'suspended' => School::where('status', 'suspended')->count(),
            'total_applications' => Application::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
        ];

        return view('super-admin.reports.schools', compact('schools', 'stats'));
    }

    public function programs(Request $request)
    {
        $query = Program::with(['school', 'department'])
            ->withCount(['applications'])
            ->when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))
            ->when($request->filled('level'), fn($q) => $q->where('level', $request->level))
            ->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'));

        $programs = $query->orderBy('applications_count', 'desc')->paginate(25);

        $schools = School::active()->orderBy('name')->get();
        $levels = Program::select('level')->distinct()->pluck('level');

        $stats = [
            'total' => Program::count(),
            'active' => Program::where('is_active', true)->count(),
            'popular' => Program::withCount('applications')->orderBy('applications_count', 'desc')->first(),
        ];

        return view('super-admin.reports.programs', compact('programs', 'schools', 'levels', 'stats'));
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['application.student.user', 'application.program', 'application.school'])
            ->when($request->filled('school_id'), fn($q) => $q->whereHas('application', fn($aq) => $aq->where('school_id', $request->school_id)))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $payments = $query->orderBy('created_at', 'desc')->paginate(25);

        $schools = School::active()->orderBy('name')->get();

        $stats = [
            'total' => Payment::sum('amount'),
            'completed' => Payment::where('status', 'completed')->sum('amount'),
            'pending' => Payment::where('status', 'pending')->sum('amount'),
            'failed' => Payment::where('status', 'failed')->sum('amount'),
            'count' => Payment::count(),
        ];

        return view('super-admin.reports.payments', compact('payments', 'schools', 'stats'));
    }

    public function users(Request $request)
    {
        $query = User::with(['school', 'roles'])
            ->when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))
            ->when($request->filled('role'), fn($q) => $q->role($request->role))
            ->when($request->filled('status'), fn($q) => $q->where('is_active', $request->status === 'active'));

        $users = $query->orderBy('created_at', 'desc')->paginate(25);

        $schools = School::active()->orderBy('name')->get();
        $roles = ['admin', 'registrar', 'accountant', 'reviewer', 'support', 'student'];

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admins' => User::role(['admin', 'super_admin'])->count(),
            'students' => User::role('student')->count(),
        ];

        return view('super-admin.reports.users', compact('users', 'schools', 'roles', 'stats'));
    }

    public function analytics(Request $request)
    {
        $months = $request->filled('months') ? (int)$request->months : 12;
        $schoolId = $request->filled('school_id') ? $request->school_id : null;

        $driver = DB::connection()->getDriverName();
        $yearExpr = $driver === 'sqlite' ? "strftime('%Y', created_at)" : 'YEAR(created_at)';
        $monthExpr = $driver === 'sqlite' ? "strftime('%m', created_at)" : 'MONTH(created_at)';

        $applicationsByMonth = Application::select(
                DB::raw("{$yearExpr} as year"),
                DB::raw("{$monthExpr} as month"),
                DB::raw('COUNT(*) as count')
            )
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $applicationsByStatus = Application::select('status', DB::raw('COUNT(*) as count'))
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->groupBy('status')
            ->pluck('count', 'status');

        $topPrograms = Program::select('programs.id', 'programs.name', DB::raw('COUNT(applications.id) as application_count'))
            ->leftJoin('applications', 'programs.id', '=', 'applications.program_id')
            ->when($schoolId, fn($q) => $q->where('programs.school_id', $schoolId))
            ->groupBy('programs.id', 'programs.name')
            ->orderBy('application_count', 'desc')
            ->limit(10)
            ->get();

        $revenueByMonth = Payment::select(
                DB::raw("{$yearExpr} as year"),
                DB::raw("{$monthExpr} as month"),
                DB::raw('SUM(amount) as total')
            )
            ->when($schoolId, fn($q) => $q->whereHas('application', fn($aq) => $aq->where('school_id', $schoolId)))
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $revenueExpr = $driver === 'sqlite' 
            ? 'SUM(CASE WHEN payments.status = "completed" THEN payments.amount ELSE 0 END)' 
            : 'SUM(CASE WHEN payments.status = "completed" THEN payments.amount ELSE 0 END)';

        $schoolsSummary = School::select('schools.id', 'schools.name', DB::raw('COUNT(DISTINCT applications.id) as applications'), DB::raw($revenueExpr . ' as revenue'))
            ->leftJoin('applications', 'schools.id', '=', 'applications.school_id')
            ->leftJoin('payments', 'applications.id', '=', 'payments.application_id')
            ->when($schoolId, fn($q) => $q->where('schools.id', $schoolId))
            ->groupBy('schools.id', 'schools.name')
            ->orderBy('applications', 'desc')
            ->limit(10)
            ->get();

        $schools = School::active()->orderBy('name')->get();

        $overview = [
            'total_applications' => Application::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count(),
            'total_students' => Student::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count(),
            'total_programs' => Program::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count(),
            'total_revenue' => Payment::when($schoolId, fn($q) => $q->whereHas('application', fn($aq) => $aq->where('school_id', $schoolId)))->where('status', 'completed')->sum('amount'),
        ];

        return view('super-admin.reports.analytics', compact(
            'applicationsByMonth',
            'applicationsByStatus',
            'topPrograms',
            'revenueByMonth',
            'schoolsSummary',
            'schools',
            'overview',
            'months'
        ));
    }

    public function export(Request $request, string $type)
    {
        $format = $request->get('format', 'csv');

        return match($type) {
            'applications' => $this->exportApplications($request, $format),
            'payments' => $this->exportPayments($request, $format),
            'schools' => $this->exportSchools($request, $format),
            'users' => $this->exportUsers($request, $format),
            default => redirect()->back()->with('error', 'Invalid report type.'),
        };
    }

    protected function exportApplications(Request $request, string $format)
    {
        $applications = Application::with(['student.user', 'program', 'intake', 'school'])
            ->when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->downloadCsv($applications->map(function ($app) {
            return [
                $app->application_number,
                $app->student?->user?->fullName() ?? 'N/A',
                $app->student?->user?->email ?? 'N/A',
                $app->school?->name ?? 'N/A',
                $app->program?->name ?? 'N/A',
                $app->intake?->name ?? 'N/A',
                ucfirst($app->status),
                $app->created_at->format('Y-m-d'),
            ];
        }), 'applications-report', ['Application Number', 'Student Name', 'Email', 'School', 'Program', 'Intake', 'Status', 'Date']);
    }

    protected function exportPayments(Request $request, string $format)
    {
        $payments = Payment::with(['application.student.user', 'application.program'])
            ->when($request->filled('school_id'), fn($q) => $q->whereHas('application', fn($aq) => $aq->where('school_id', $request->school_id)))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->downloadCsv($payments->map(function ($payment) {
            return [
                $payment->transaction_id,
                $payment->application?->student?->user?->fullName() ?? 'N/A',
                $payment->application?->program?->name ?? 'N/A',
                $payment->amount,
                $payment->currency,
                ucfirst($payment->status),
                $payment->created_at->format('Y-m-d H:i'),
            ];
        }), 'payments-report', ['Transaction ID', 'Student', 'Program', 'Amount', 'Currency', 'Status', 'Date']);
    }

    protected function exportSchools(Request $request, string $format)
    {
        $schools = School::withCount(['applications', 'users', 'programs'])
            ->orderBy('name')
            ->get();

        return $this->downloadCsv($schools->map(function ($school) {
            return [
                $school->name,
                $school->code,
                $school->email,
                $school->status,
                $school->applications_count,
                $school->users_count,
                $school->programs_count,
            ];
        }), 'schools-report', ['School Name', 'Code', 'Email', 'Status', 'Applications', 'Users', 'Programs']);
    }

    protected function exportUsers(Request $request, string $format)
    {
        $users = User::with(['school', 'roles'])
            ->when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->downloadCsv($users->map(function ($user) {
            return [
                $user->fullName(),
                $user->email,
                $user->phone ?? 'N/A',
                $user->school?->name ?? 'System',
                $user->roles->pluck('name')->implode(', '),
                $user->is_active ? 'Active' : 'Inactive',
                $user->created_at->format('Y-m-d'),
            ];
        }), 'users-report', ['Name', 'Email', 'Phone', 'School', 'Roles', 'Status', 'Created']);
    }

    protected function downloadCsv($data, string $filename, array $headers): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->streamDownload(function () use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, "{$filename}-" . date('Y-m-d') . ".csv", [
            'Content-Type' => 'text/csv',
        ]);
    }
}
