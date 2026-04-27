<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function activeSessions()
    {
        $user = auth()->user();
        
        $sessions = [];
        
        if (config('session.driver') === 'database') {
            $sessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->get();
        }

        return view('auth.sessions', compact('sessions'));
    }

    public function logoutFromAllDevices(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Incorrect password.'], 422);
            }
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $user->logoutFromAllDevices();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('login')->with('success', 'You have been logged out from all devices.');
    }

    public function terminateSession(Request $request, $sessionId)
    {
        if (config('session.driver') === 'database') {
            DB::table('sessions')
                ->where('id', $sessionId)
                ->where('user_id', auth()->id())
                ->delete();
        }

        Session::flush();
        auth()->logout();

        return redirect()->route('login');
    }
}
