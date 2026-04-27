<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Application;
use App\Models\Program;
use App\Models\Intake;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = Application::with(['student.user', 'program', 'intake', 'school']);

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('intake_id')) {
            $query->where('intake_id', $request->intake_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhereHas('student.user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(25);

        $schools = School::active()->orderBy('name')->get();
        $programs = Program::active()->orderBy('name')->get();
        $intakes = Intake::where('is_active', true)->orderBy('application_start_date', 'desc')->get();

        $stats = [
            'total' => Application::when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))->count(),
            'pending' => Application::when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))->where('status', 'pending')->count(),
            'approved' => Application::when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))->where('status', 'approved')->count(),
            'rejected' => Application::when($request->filled('school_id'), fn($q) => $q->where('school_id', $request->school_id))->where('status', 'rejected')->count(),
        ];

        return view('super-admin.applications.index', compact('applications', 'schools', 'programs', 'intakes', 'stats'));
    }

    public function show(Application $application)
    {
        $application->load([
            'student.user',
            'student.school',
            'program',
            'intake',
            'documents',
            'payments',
            'admissionLetter',
        ]);

        $timeline = $this->getApplicationTimeline($application);

        return view('super-admin.applications.show', compact('application', 'timeline'));
    }

    public function showBySchool(School $school, Request $request)
    {
        School::setCurrentId($school->id);

        $query = Application::with(['student.user', 'program', 'intake'])
            ->where('school_id', $school->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('intake_id')) {
            $query->where('intake_id', $request->intake_id);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(25);
        $intakes = Intake::where('school_id', $school->id)->where('is_active', true)->orderBy('application_start_date', 'desc')->get();

        return view('super-admin.applications.by-school', compact('school', 'applications', 'intakes'));
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:applications,id',
            'action' => 'required|in:approve,reject,mark_reviewing',
            'notes' => 'nullable|string|max:1000',
        ]);

        $applications = Application::whereIn('id', $request->application_ids);
        $count = $applications->count();

        switch ($request->action) {
            case 'approve':
                foreach ($applications->get() as $application) {
                    $application->update([
                        'status' => 'approved',
                        'reviewed_at' => now(),
                        'reviewed_by' => auth()->id(),
                        'review_notes' => $request->notes,
                    ]);
                    AuditLog::log('approve_application', $application, null, ['status' => 'approved', 'bulk' => true]);
                    $application->user->notify(new \App\Notifications\ApplicationApproved($application));
                }
                break;
            case 'reject':
                foreach ($applications->get() as $application) {
                    $application->update([
                        'status' => 'rejected',
                        'reviewed_at' => now(),
                        'reviewed_by' => auth()->id(),
                        'review_notes' => $request->notes,
                    ]);
                    AuditLog::log('reject_application', $application, null, ['status' => 'rejected', 'bulk' => true]);
                    $application->user->notify(new \App\Notifications\ApplicationRejected($application));
                }
                break;
            case 'mark_reviewing':
                $applications->update(['status' => 'under_review']);
                break;
        }

        return redirect()->back()->with('success', "{$count} applications updated successfully.");
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

        return redirect()->back()->with('success', 'Application approved successfully!');
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

        return redirect()->back()->with('success', 'Application rejected.');
    }

    public function export(Request $request)
    {
        $request->validate([
            'school_id' => 'nullable|exists:schools,id',
            'status' => 'nullable|in:pending,reviewing,approved,rejected',
            'format' => 'nullable|in:csv,xlsx',
        ]);

        $query = Application::with(['student.user', 'program', 'intake', 'school']);

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="applications-' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['Application Number', 'Student Name', 'Email', 'Phone', 'School', 'Program', 'Intake', 'Status', 'Applied Date'];

        $callback = function () use ($applications, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($applications as $app) {
                fputcsv($handle, [
                    $app->application_number,
                    $app->student?->user?->fullName() ?? 'N/A',
                    $app->student?->user?->email ?? 'N/A',
                    $app->student?->user?->phone ?? 'N/A',
                    $app->school?->name ?? 'N/A',
                    $app->program?->name ?? 'N/A',
                    $app->intake?->name ?? 'N/A',
                    ucfirst($app->status),
                    $app->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getApplicationTimeline(Application $application): array
    {
        $timeline = [
            ['date' => $application->created_at, 'event' => 'Application Submitted', 'type' => 'submission'],
        ];

        if ($application->submitted_at) {
            $timeline[] = ['date' => $application->submitted_at, 'event' => 'Application Sent for Review', 'type' => 'review'];
        }

        if ($application->reviewed_at) {
            $statusLabel = match($application->status) {
                'approved' => 'Application Approved',
                'rejected' => 'Application Rejected',
                'reviewing' => 'Under Review',
                default => 'Status Updated',
            };
            $timeline[] = ['date' => $application->reviewed_at, 'event' => $statusLabel, 'type' => $application->status];
        }

        if ($application->admissionLetter) {
            $timeline[] = ['date' => $application->admissionLetter->created_at, 'event' => 'Offer Letter Generated', 'type' => 'offer'];
        }

        return $timeline;
    }

    public function getProgramsBySchool(Request $request)
    {
        $request->validate(['school_id' => 'required|exists:schools,id']);

        $programs = Program::where('school_id', $request->school_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($programs);
    }

    public function getIntakesBySchool(Request $request)
    {
        $request->validate(['school_id' => 'required|exists:schools,id']);

        $intakes = Intake::where('school_id', $request->school_id)
            ->active()
            ->orderBy('application_start_date', 'desc')
            ->get(['id', 'name', 'application_start_date', 'application_end_date']);

        return response()->json($intakes);
    }
}
