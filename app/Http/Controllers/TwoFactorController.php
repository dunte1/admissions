<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function show()
    {
        $user = auth()->user();
        
        if ($user->hasTwoFactorEnabled()) {
            return view('auth.two-factor-verify');
        }

        if (session('two_factor_secret')) {
            $secret = session('two_factor_secret');
            $qrCodeUrl = $user->getTwoFactorQrCodeUrl();
            return view('auth.two-factor-setup', compact('secret', 'qrCodeUrl'));
        }

        return redirect()->route('home');
    }

    public function setup(Request $request)
    {
        $user = auth()->user();
        
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('home');
        }

        $result = $user->enableTwoFactor();
        
        Session::put('two_factor_secret', $result['secret']);
        Session::put('two_factor_recovery_codes', $result['recovery_codes']);

        return redirect()->route('two-factor.show');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        if ($user->verifyTwoFactorCode($request->code)) {
            $user->confirmTwoFactor();
            
            $recoveryCodes = Session::get('two_factor_recovery_codes', []);
            Session::forget(['two_factor_secret', 'two_factor_recovery_codes']);
            Session::put('two_factor_recovery_codes_shown', $recoveryCodes);

            return redirect()->route('home')->with('success', 'Two-factor authentication enabled successfully!');
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }

    public function verifyWithRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $user = auth()->user();

        if ($user->verifyRecoveryCode($request->recovery_code)) {
            Session::put('two_factor_verified', true);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->intended(route('home'));
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Invalid recovery code.'], 422);
        }

        return back()->withErrors(['recovery_code' => 'Invalid recovery code.']);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $user->disableTwoFactor();

        return redirect()->back()->with('success', 'Two-factor authentication has been disabled.');
    }

    public function regenerateCodes(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        if (!$user->hasTwoFactorEnabled()) {
            return back()->withErrors(['error' => 'Two-factor authentication is not enabled.']);
        }

        $codes = $user->regenerateRecoveryCodes();

        return redirect()->back()->with([
            'success' => 'Recovery codes regenerated successfully!',
            'recovery_codes' => $codes,
        ]);
    }
}
