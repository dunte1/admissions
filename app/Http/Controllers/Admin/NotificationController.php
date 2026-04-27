<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,registrar']);
    }

    public function send()
    {
        $applications = Application::with('user')->get();
        $statuses = Application::distinct()->pluck('status');

        return view('admin.notifications.send', compact('applications', 'statuses'));
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:all,status,individual',
            'status' => 'required_if:recipient_type,status',
            'application_ids' => 'required_if:recipient_type,individual|array',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'notification_type' => 'required|in:email,sms,both',
        ]);

        $query = Application::with('user');

        if ($request->recipient_type === 'status' && $request->status) {
            $query->where('status', $request->status);
        } elseif ($request->recipient_type === 'individual' && $request->application_ids) {
            $query->whereIn('id', $request->application_ids);
        }

        $applications = $query->get();

        foreach ($applications as $application) {
            if ($request->notification_type === 'email' || $request->notification_type === 'both') {
                $this->sendEmail($application, $request->subject, $request->message);
            }
        }

        return redirect()->route('admin.notifications.send')
            ->with('success', "Notification sent to {$applications->count()} recipients");
    }

    protected function sendEmail($application, $subject, $message)
    {
        try {
            Mail::raw($message, function ($mail) use ($application, $subject) {
                $mail->to($application->user->email)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error("Email failed for {$application->user->email}: " . $e->getMessage());
        }
    }
}
