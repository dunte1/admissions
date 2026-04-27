<?php

namespace App\Http\Controllers\SuperAdmin;

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
        $user = auth()->user();
        return view('super-admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
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

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function notifications()
    {
        $user = auth()->user();
        
        $notifications = $user->notifications()
            ->when(request('type') === 'unread', function ($query) {
                return $query->whereNull('read_at');
            })
            ->when(request('type') === 'read', function ($query) {
                return $query->whereNotNull('read_at');
            })
            ->paginate(15);

        return view('super-admin.profile.notifications', compact('notifications', 'user'));
    }

    public function markNotificationRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    public function markAllNotificationsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
