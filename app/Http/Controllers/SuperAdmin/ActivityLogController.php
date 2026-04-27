<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = ActivityLog::withoutGlobalScopes()->with(['user', 'school']);

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        } elseif ($request->filled('search_school')) {
            $schoolIds = School::where('name', 'like', '%' . $request->search_school . '%')
                ->pluck('id');
            $query->whereIn('school_id', $schoolIds);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        } elseif ($request->filled('search_user')) {
            $userIds = User::where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search_user . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search_user . '%')
                  ->orWhere('email', 'like', '%' . $request->search_user . '%');
            })->pluck('id');
            $query->whereIn('user_id', $userIds);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest('created_at')->paginate(50)->withQueryString();
        $actions = ActivityLog::withoutGlobalScopes()->distinct()->pluck('action')->filter();
        $modules = ActivityLog::withoutGlobalScopes()->distinct()->pluck('module')->filter();
        $schools = School::orderBy('name')->get();

        $stats = [
            'total' => ActivityLog::withoutGlobalScopes()->count(),
            'today' => ActivityLog::withoutGlobalScopes()->whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::withoutGlobalScopes()->where('created_at', '>=', now()->startOfWeek())->count(),
            'unique_users' => ActivityLog::withoutGlobalScopes()->distinct('user_id')->count(),
        ];

        return view('super-admin.activity-logs', compact(
            'logs', 'actions', 'modules', 'schools', 'stats'
        ));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::withoutGlobalScopes()->with(['user', 'school']);

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest('created_at')->limit(10000)->get();

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['ID', 'Timestamp', 'School', 'User', 'Role', 'Action', 'Module', 'Description', 'IP Address', 'URL']);

        foreach ($logs as $log) {
            fputcsv($csv, [
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->school?->name ?? 'N/A',
                $log->user?->fullName() ?? 'System',
                $log->role ?? 'N/A',
                $log->action,
                $log->module ?? 'N/A',
                $log->description ?? '',
                $log->ip_address ?? 'N/A',
                $log->url ?? '',
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function statistics()
    {
        $stats = [
            'by_action' => ActivityLog::withoutGlobalScopes()
                ->select('action', DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),

            'by_module' => ActivityLog::withoutGlobalScopes()
                ->select('module', DB::raw('count(*) as count'))
                ->groupBy('module')
                ->orderByDesc('count')
                ->get(),

            'by_school' => ActivityLog::withoutGlobalScopes()
                ->select('school_id', DB::raw('count(*) as count'))
                ->groupBy('school_id')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),

            'recent_users' => ActivityLog::withoutGlobalScopes()
                ->select('user_id', DB::raw('max(created_at) as last_activity'), DB::raw('count(*) as activity_count'))
                ->groupBy('user_id')
                ->orderByDesc('last_activity')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }
}
