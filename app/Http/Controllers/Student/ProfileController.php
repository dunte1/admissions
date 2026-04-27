<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    protected FileUploadService $fileUpload;

    public function __construct(FileUploadService $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    public function edit()
    {
        return view('student.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'preferred_language' => 'required|in:en,sw',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $path = $this->fileUpload->uploadFile(
                $request->file('photo'),
                'profile',
                $user->photo ?? null
            );
            $validated['photo'] = $path;
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    public function settings()
    {
        $user = auth()->user();
        return view('student.settings.index', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'preferred_language' => 'required|in:en,sw',
            'dark_mode' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ]);

        $user->update([
            'preferred_language' => $validated['preferred_language'],
            'dark_mode' => $request->boolean('dark_mode'),
        ]);

        $user->setSettings([
            'email_notifications' => $request->boolean('email_notifications') ? '1' : '0',
            'sms_notifications' => $request->boolean('sms_notifications') ? '1' : '0',
        ]);

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function notifications(Request $request)
    {
        $user = auth()->user();
        
        $notifications = $user->notifications()
            ->when($request->type === 'unread', function ($query) {
                return $query->whereNull('read_at');
            })
            ->when($request->type === 'read', function ($query) {
                return $query->whereNotNull('read_at');
            })
            ->paginate(15);

        return view('student.notifications.index', compact('notifications'));
    }

    public function markNotificationRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    public function toggleDarkMode(Request $request)
    {
        $user = auth()->user();
        $user->update(['dark_mode' => $request->boolean('dark_mode')]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'dark_mode' => $user->dark_mode]);
        }

        return redirect()->back();
    }
}
