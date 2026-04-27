<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view_audit_logs']);
    }

    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'school']);

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

        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        if ($request->filled('model_id')) {
            $query->where('model_id', $request->model_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(50)->withQueryString();
        $actions = AuditLog::distinct()->pluck('action')->filter();
        $users = User::orderBy('first_name')->get();

        $stats = [
            'total' => AuditLog::count(),
            'today' => AuditLog::whereDate('created_at', today())->count(),
            'creates' => AuditLog::where('action', 'create')->count(),
            'updates' => AuditLog::where('action', 'update')->count(),
            'deletes' => AuditLog::where('action', 'delete')->count(),
        ];

        return view('admin.audit-logs.index', compact('logs', 'actions', 'users', 'stats'));
    }

    public function show(AuditLog $auditLog)
    {
        if ($auditLog->school_id && $auditLog->school_id !== auth()->user()->school_id && !auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $auditLog->load(['user', 'school']);

        return view('admin.audit-logs.show', compact('auditLog'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with(['user']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->limit(10000)->get();

        $csv = fopen('php://temp', 'r+');
        fputcsv($csv, ['ID', 'Timestamp', 'User', 'Action', 'Model', 'Model ID', 'Old Values', 'New Values', 'IP Address']);

        foreach ($logs as $log) {
            fputcsv($csv, [
                $log->id,
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user?->fullName() ?? 'System',
                $log->action,
                class_basename($log->model_type) ?? 'N/A',
                $log->model_id ?? 'N/A',
                $log->old_values ? json_encode($log->old_values) : '',
                $log->new_values ? json_encode($log->new_values) : '',
                $log->ip_address ?? 'N/A',
            ]);
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        $filename = 'audit_logs_' . date('Y-m-d_His') . '.csv';

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function statistics()
    {
        $stats = [
            'by_action' => AuditLog::select('action', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('action')
                ->orderByDesc('count')
                ->get(),

            'by_model' => AuditLog::select('model_type', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->whereNotNull('model_type')
                ->groupBy('model_type')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),

            'by_user' => AuditLog::select('user_id', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->groupBy('user_id')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats);
    }
}
