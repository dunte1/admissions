<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\BroadcastRecipient;
use App\Models\BroadcastTemplate;
use App\Models\User;
use App\Jobs\SendBroadcastJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SchoolBroadcastController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,registrar,accountant', 'school.scope', 'subscription']);
        $this->middleware('permission:send_notifications')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        
        $query = Broadcast::forSchool($schoolId)
            ->with(['user'])
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $broadcasts = $query->paginate(15);

        $stats = [
            'total' => Broadcast::forSchool($schoolId)->count(),
            'sent' => Broadcast::forSchool($schoolId)->where('status', 'sent')->count(),
            'scheduled' => Broadcast::forSchool($schoolId)->where('status', 'scheduled')->count(),
        ];

        return view('admin.broadcast.index', compact('broadcasts', 'stats'));
    }

    public function create()
    {
        $schoolId = auth()->user()->school_id;
        
        $roles = ['student', 'admin', 'registrar', 'reviewer', 'accountant', 'support'];
        $applicationStatuses = ['pending', 'under_review', 'approved', 'rejected', 'info_requested'];
        $templates = BroadcastTemplate::forSchool($schoolId)->active()->get();

        return view('admin.broadcast.create', compact('roles', 'applicationStatuses', 'templates'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'type' => 'required|in:in_app,email,sms,all',
            'targeting' => 'nullable|array',
            'targeting.roles' => 'nullable|array',
            'targeting.application_status' => 'nullable|array',
            'schedule_type' => 'required|in:now,schedule',
            'scheduled_at' => 'required_if:schedule_type,schedule|date|after:now',
            'template_id' => 'nullable|exists:broadcast_templates,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $schoolId = auth()->user()->school_id;

        DB::beginTransaction();
        try {
            $targeting = $request->targeting ?? [];
            
            if (empty($targeting['roles'])) {
                $targeting['roles'] = [];
            }

            if ($request->template_id) {
                $template = BroadcastTemplate::find($request->template_id);
                if ($template) {
                    $request->merge([
                        'title' => Broadcast::parseVariables($request->title, ['school_name' => auth()->user()->school?->name]),
                        'message' => Broadcast::parseVariables($template->content, ['school_name' => auth()->user()->school?->name]),
                    ]);
                }
            }

            $recipients = $this->getRecipients($schoolId, $targeting);

            if ($recipients->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'No recipients found for the selected targeting criteria.')
                    ->withInput();
            }

            $broadcast = Broadcast::create([
                'user_id' => auth()->id(),
                'school_id' => $schoolId,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'targeting' => $targeting,
                'status' => $request->schedule_type === 'schedule' ? 'scheduled' : 'draft',
                'scheduled_at' => $request->schedule_type === 'schedule' ? $request->scheduled_at : null,
                'total_recipients' => $recipients->count(),
            ]);

            foreach ($recipients as $user) {
                BroadcastRecipient::create([
                    'broadcast_id' => $broadcast->id,
                    'user_id' => $user->id,
                    'school_id' => $user->school_id,
                    'status' => 'pending',
                ]);
            }

            if ($request->schedule_type === 'now') {
                SendBroadcastJob::dispatch($broadcast);
            } else {
                SendBroadcastJob::dispatch($broadcast)->delay(
                    now()->parse($request->scheduled_at)->diffInSeconds(now())
                );
            }

            DB::commit();

            $message = $request->schedule_type === 'schedule'
                ? 'Broadcast scheduled successfully!'
                : 'Broadcast queued for sending!';

            return redirect()->route('admin.broadcast.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create broadcast: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Broadcast $broadcast)
    {
        $this->authorize('view', $broadcast);

        $broadcast->load(['user', 'recipients.user']);

        return view('admin.broadcast.show', compact('broadcast'));
    }

    public function cancel(Broadcast $broadcast)
    {
        $this->authorize('update', $broadcast);

        if ($broadcast->status === 'sent') {
            return redirect()->back()
                ->with('error', 'Cannot cancel a broadcast that has already been sent.');
        }

        $broadcast->cancel();

        return redirect()->back()
            ->with('success', 'Broadcast cancelled successfully.');
    }

    public function resend(Broadcast $broadcast)
    {
        $this->authorize('update', $broadcast);

        if (!in_array($broadcast->status, ['sent', 'failed'])) {
            return redirect()->back()
                ->with('error', 'Can only resend sent or failed broadcasts.');
        }

        $broadcast->update([
            'status' => 'draft',
            'sent_at' => null,
            'sent_count' => 0,
            'delivered_count' => 0,
            'failed_count' => 0,
        ]);

        BroadcastRecipient::where('broadcast_id', $broadcast->id)->update([
            'status' => 'pending',
            'sent_at' => null,
            'delivered_at' => null,
            'read_at' => null,
        ]);

        SendBroadcastJob::dispatch($broadcast);

        return redirect()->back()
            ->with('success', 'Broadcast queued for resending!');
    }

    protected function getRecipients(int $schoolId, array $targeting): \Illuminate\Support\Collection
    {
        $query = User::query()
            ->where('school_id', $schoolId)
            ->where('is_active', true);

        if (!empty($targeting['roles'])) {
            $query->whereHas('roles', function ($q) use ($targeting) {
                $q->whereIn('name', $targeting['roles']);
            });
        }

        if (!empty($targeting['application_status'])) {
            $query->whereHas('student.applications', function ($q) use ($targeting) {
                $q->whereIn('status', $targeting['application_status']);
            });
        }

        return $query->get();
    }

    public function templates()
    {
        $schoolId = auth()->user()->school_id;
        $templates = BroadcastTemplate::forSchool($schoolId)->active()->get();

        return view('admin.broadcast.templates', compact('templates'));
    }

    public function createTemplate()
    {
        return view('admin.broadcast.create-template');
    }

    public function storeTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string|max:5000',
            'type' => 'required|in:in_app,email,sms,all',
            'category' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        BroadcastTemplate::create([
            'user_id' => auth()->id(),
            'school_id' => auth()->user()->school_id,
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'type' => $request->type,
            'category' => $request->category,
        ]);

        return redirect()->route('admin.broadcast.templates')
            ->with('success', 'Template created successfully!');
    }

    public function editTemplate(BroadcastTemplate $template)
    {
        if ($template->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        return view('admin.broadcast.edit-template', compact('template'));
    }

    public function updateTemplate(Request $request, BroadcastTemplate $template)
    {
        if ($template->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string|max:5000',
            'type' => 'required|in:in_app,email,sms,all',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $template->update($request->only(['name', 'subject', 'content', 'type', 'category', 'is_active']));

        return redirect()->route('admin.broadcast.templates')
            ->with('success', 'Template updated successfully!');
    }
}
