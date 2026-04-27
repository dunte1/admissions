<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\BroadcastLog;
use App\Models\BroadcastRecipient;
use App\Models\School;
use App\Models\User;
use App\Jobs\SendBroadcastJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class BroadcastController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = Broadcast::query()
            ->with(['user', 'school'])
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
            'total' => Broadcast::count(),
            'sent' => Broadcast::where('status', 'sent')->count(),
            'scheduled' => Broadcast::where('status', 'scheduled')->count(),
            'sent_count' => Broadcast::sum('sent_count'),
        ];

        return view('super-admin.broadcast.index', compact('broadcasts', 'stats'));
    }

    public function create()
    {
        $schools = School::where('status', 'active')->orderBy('name')->get();
        $roles = ['student', 'admin', 'registrar', 'reviewer', 'accountant', 'support'];
        $applicationStatuses = ['pending', 'under_review', 'approved', 'rejected', 'info_requested'];

        return view('super-admin.broadcast.create', compact('schools', 'roles', 'applicationStatuses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'type' => 'required|in:in_app,email,sms,all',
            'targeting' => 'nullable|array',
            'targeting.schools' => 'nullable',
            'targeting.roles' => 'nullable|array',
            'targeting.application_status' => 'nullable|array',
            'schedule_type' => 'required|in:now,schedule',
            'scheduled_at' => 'required_if:schedule_type,schedule|date|after:now',
            'is_test' => 'nullable|boolean',
            'test_email' => 'nullable|email|required_if:is_test,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $targeting = $request->targeting ?? [];

            if (empty($targeting)) {
                $targeting = ['schools' => 'all', 'roles' => [], 'application_status' => []];
            }

            $recipients = $this->getRecipients($targeting, $request->is_test, $request->test_email);

            if ($recipients->isEmpty() && !$request->is_test) {
                return redirect()->back()
                    ->with('error', 'No recipients found for the selected targeting criteria.')
                    ->withInput();
            }

            $broadcast = Broadcast::create([
                'user_id' => auth()->id(),
                'school_id' => null,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'targeting' => $targeting,
                'status' => $request->schedule_type === 'schedule' ? 'scheduled' : 'draft',
                'scheduled_at' => $request->schedule_type === 'schedule' ? $request->scheduled_at : null,
                'total_recipients' => $request->is_test ? 1 : $recipients->count(),
                'is_test' => $request->is_test ?? false,
            ]);

            BroadcastLog::logCreated($broadcast);

            if ($request->schedule_type === 'schedule') {
                BroadcastLog::scheduled($broadcast, $request->scheduled_at);
            }

            if ($request->is_test) {
                $testUser = User::where('email', $request->test_email)->first();
                if (!$testUser) {
                    $testUser = auth()->user();
                }

                BroadcastRecipient::create([
                    'broadcast_id' => $broadcast->id,
                    'user_id' => $testUser->id,
                    'school_id' => $testUser->school_id,
                    'status' => 'pending',
                ]);

                SendBroadcastJob::dispatch($broadcast, collect([$testUser]));
            } else {
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
            }

            DB::commit();

            $message = $request->schedule_type === 'schedule'
                ? 'Broadcast scheduled successfully!'
                : 'Broadcast queued for sending!';

            return redirect()->route('super-admin.broadcast.index')
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
        $broadcast->load(['user', 'school', 'recipients.user']);

        $logs = $broadcast->logs()->orderBy('created_at', 'desc')->get();

        return view('super-admin.broadcast.show', compact('broadcast', 'logs'));
    }

    public function destroy(Broadcast $broadcast)
    {
        if (in_array($broadcast->status, ['sending', 'sent'])) {
            return redirect()->back()
                ->with('error', 'Cannot delete a broadcast that is sending or has been sent.');
        }

        $broadcast->delete();

        return redirect()->route('super-admin.broadcast.index')
            ->with('success', 'Broadcast deleted successfully.');
    }

    public function cancel(Broadcast $broadcast)
    {
        if ($broadcast->status === 'sent') {
            return redirect()->back()
                ->with('error', 'Cannot cancel a broadcast that has already been sent.');
        }

        $broadcast->cancel();
        BroadcastLog::cancelled($broadcast);

        return redirect()->back()
            ->with('success', 'Broadcast cancelled successfully.');
    }

    public function resend(Broadcast $broadcast)
    {
        if ($broadcast->status !== 'sent' && $broadcast->status !== 'failed') {
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
            'error_message' => null,
        ]);

        SendBroadcastJob::dispatch($broadcast);

        return redirect()->back()
            ->with('success', 'Broadcast queued for resending!');
    }

    public function preview(Broadcast $broadcast)
    {
        $recipients = $broadcast->recipients()
            ->with('user')
            ->paginate(20);

        return view('super-admin.broadcast.preview', compact('broadcast', 'recipients'));
    }

    public function getRecipients(array $targeting, bool $isTest = false, ?string $testEmail = null): \Illuminate\Support\Collection
    {
        $query = User::query();

        if (!empty($targeting['schools'])) {
            if ($targeting['schools'] !== 'all') {
                $query->whereIn('school_id', $targeting['schools']);
            }
        }

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

        if ($isTest && $testEmail) {
            $query->where('email', $testEmail);
        }

        $query->where('is_active', true);

        return $query->get()->filter(function ($user) {
            return $user->school_id === null || $user->school?->status === 'active';
        });
    }

    public function stats(): \Illuminate\Http\JsonResponse
    {
        $stats = [
            'total' => Broadcast::count(),
            'sent' => Broadcast::where('status', 'sent')->count(),
            'scheduled' => Broadcast::where('status', 'scheduled')->count(),
            'failed' => Broadcast::where('status', 'failed')->count(),
            'total_recipients' => BroadcastRecipient::where('status', '!=', 'pending')->count(),
            'delivered' => BroadcastRecipient::where('status', 'delivered')->count(),
            'read' => BroadcastRecipient::where('status', 'read')->count(),
        ];

        return response()->json($stats);
    }
}
