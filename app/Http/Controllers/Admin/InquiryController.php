<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\InquiryReply;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $query = Inquiry::with(['user', 'application', 'assignedTo']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->assigned_to === 'me') {
            $query->where('assigned_to', auth()->id());
        } elseif ($request->assigned_to === 'unassigned') {
            $query->whereNull('assigned_to');
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                    ->orWhere('message', 'like', "%{$request->search}%")
                    ->orWhereHas('user', function ($uq) use ($request) {
                        $uq->where('email', 'like', "%{$request->search}%")
                            ->orWhere('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name', 'like', "%{$request->search}%");
                    });
            });
        }

        $inquiries = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Inquiry::count(),
            'open' => Inquiry::where('status', 'open')->count(),
            'in_progress' => Inquiry::where('status', 'in_progress')->count(),
            'resolved' => Inquiry::where('status', 'resolved')->count(),
            'urgent' => Inquiry::where('priority', 'urgent')->whereIn('status', ['open', 'in_progress'])->count(),
        ];

        $staff = \App\Models\User::where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'support', 'registrar']);
            })->get();

        return view('admin.inquiries.index', compact('inquiries', 'stats', 'staff'));
    }

    public function show(Inquiry $inquiry)
    {
        $inquiry->load(['user', 'application', 'assignedTo', 'replies.user']);

        $staff = \App\Models\User::where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'support', 'registrar']);
            })->get();

        return view('admin.inquiries.show', compact('inquiry', 'staff'));
    }

    public function create()
    {
        $applications = \App\Models\Application::where('school_id', current_school_id())
            ->latest()
            ->limit(50)
            ->get(['id', 'application_number', 'first_name', 'last_name']);

        return view('admin.inquiries.create', compact('applications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'required|in:low,medium,high,urgent',
            'application_id' => 'nullable|exists:applications,id',
        ]);

        $schoolId = current_school_id();

        $inquiry = Inquiry::create([
            'user_id' => auth()->id(),
            'school_id' => $schoolId,
            'application_id' => $request->application_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return redirect()->route('admin.inquiries.show', $inquiry)
            ->with('success', 'Inquiry created successfully.');
    }

    public function assign(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $inquiry->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress',
        ]);

        return redirect()->back()->with('success', 'Inquiry assigned successfully.');
    }

    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $update = ['status' => $request->status];
        
        if ($request->status === 'resolved') {
            $update['resolved_at'] = now();
        }

        $inquiry->update($update);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function reply(Request $request, Inquiry $inquiry)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'is_internal' => 'boolean',
        ]);

        InquiryReply::create([
            'inquiry_id' => $inquiry->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_internal' => $request->boolean('is_internal'),
        ]);

        if ($inquiry->status === 'open') {
            $inquiry->update(['status' => 'in_progress']);
        }

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }
}

