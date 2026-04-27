<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class SupportDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $this->authorize('view_applications');

        $school = current_school();

        $stats = [
            'total_applications' => Application::forSchool($school->id)->count(),
            'pending_applications' => Application::forSchool($school->id)->where('status', 'pending')->count(),
            'total_inquiries' => Inquiry::forSchool($school->id)->count(),
            'open_inquiries' => Inquiry::forSchool($school->id)->where('status', 'open')->count(),
        ];

        $recentApplications = Application::with(['student', 'program'])
            ->latest()
            ->take(10)
            ->get();

        $recentInquiries = Inquiry::with(['user', 'school'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.support-dashboard', compact(
            'stats',
            'recentApplications',
            'recentInquiries',
            'school'
        ));
    }

    public function applications(Request $request)
    {
        $this->authorize('view_applications');

        $query = Application::with(['student', 'program', 'user']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('application_number', 'like', "%{$request->search}%")
                    ->orWhereHas('student', function ($sq) use ($request) {
                        $sq->where('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%");
                    });
            });
        }

        $applications = $query->latest()->paginate(20)->withQueryString();
        $programs = \App\Models\Program::where('is_active', true)->get();

        return view('admin.support-applications', compact('applications', 'programs'));
    }

    public function applicationShow(Application $application)
    {
        $this->authorize('view_applications');

        $application->load(['student', 'program', 'user', 'documents', 'payments', 'reviewer']);
        $applicationData = is_array($application->form_data) ? $application->form_data : [];

        return view('admin.applications.show', compact('application', 'applicationData'));
    }

    public function inquiries(Request $request)
    {
        $this->authorize('view_inquiries');

        $school = current_school();

        $query = Inquiry::with(['user', 'assignedTo']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                    ->orWhere('message', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $inquiries = $query->latest()->paginate(20)->withQueryString();

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function inquiryShow(Inquiry $inquiry)
    {
        $this->authorize('view_inquiries');

        $inquiry->load(['user', 'assignedTo', 'replies']);

        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function inquiryReply(Request $request, Inquiry $inquiry)
    {
        $this->authorize('reply_inquiries');

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $inquiry->replies()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        if ($inquiry->status === 'open') {
            $inquiry->update(['status' => 'pending']);
        }

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }
}
