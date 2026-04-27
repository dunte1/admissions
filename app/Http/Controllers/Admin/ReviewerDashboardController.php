<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:reviewer']);
    }

    public function index()
    {
        $school = current_school();
        $stats = $this->getReviewStats();
        $pendingApplications = $this->getPendingApplications();
        $recentReviews = $this->getRecentReviews();
        $programs = Program::where('is_active', true)->get();

        return view('admin.reviewer.dashboard', compact(
            'stats',
            'pendingApplications',
            'recentReviews',
            'programs',
            'school'
        ));
    }

    protected function getReviewStats(): array
    {
        $pending = Application::whereIn('status', ['pending', 'under_review'])->count();
        $reviewedToday = Application::whereDate('reviewed_at', now()->today())->count();
        $approvedThisWeek = Application::where('status', 'approved')
            ->whereDate('reviewed_at', '>=', now()->startOfWeek())
            ->count();
        $rejectedThisWeek = Application::where('status', 'rejected')
            ->whereDate('reviewed_at', '>=', now()->startOfWeek())
            ->count();

        return [
            'pending' => $pending,
            'reviewed_today' => $reviewedToday,
            'approved_this_week' => $approvedThisWeek,
            'rejected_this_week' => $rejectedThisWeek,
        ];
    }

    protected function getPendingApplications(int $limit = 15)
    {
        return Application::with(['student', 'program', 'documents'])
            ->whereIn('status', ['pending', 'under_review'])
            ->orderByRaw("FIELD(status, 'pending', 'under_review')")
            ->orderBy('created_at', 'asc')
            ->take($limit)
            ->get();
    }

    protected function getRecentReviews(int $limit = 10)
    {
        return Application::with(['student', 'program', 'reviewer'])
            ->whereNotNull('reviewed_at')
            ->latest('reviewed_at')
            ->take($limit)
            ->get();
    }

    public function applications(Request $request)
    {
        $query = Application::with(['student', 'program', 'documents', 'user']);

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['pending', 'under_review']);
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
                    });
            });
        }

        $applications = $query->latest()->paginate(20)->withQueryString();
        $programs = Program::where('is_active', true)->get();

        return view('admin.reviewer.applications', compact('applications', 'programs'));
    }

    public function startReview(Application $application)
    {
        if (!in_array($application->status, ['pending', 'under_review'])) {
            return redirect()->back()->with('error', 'This application cannot be reviewed.');
        }

        $application->update([
            'status' => 'under_review',
        ]);

        \App\Models\AuditLog::log('start_review', $application, null, ['status' => 'under_review']);

        return redirect()->route('admin.applications.show', $application)
            ->with('success', 'Review started. Application is now under review.');
    }

    public function addNote(Request $request, Application $application)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        \App\Models\ApplicationNote::create([
            'application_id' => $application->id,
            'user_id' => auth()->id(),
            'note' => $request->note,
            'is_internal' => $request->boolean('is_internal', true),
        ]);

        return redirect()->back()->with('success', 'Note added successfully.');
    }
}
