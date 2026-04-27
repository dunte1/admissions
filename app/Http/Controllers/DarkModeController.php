<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DarkModeController extends Controller
{
    public function toggle()
    {
        $user = Auth::user();
        
        if ($user) {
            $user->dark_mode = !$user->dark_mode;
            $user->save();
            session(['dark_mode' => $user->dark_mode]);
        } else {
            session(['dark_mode' => !session('dark_mode', false)]);
        }
        
        return response()->json(['dark_mode' => session('dark_mode')]);
    }
}
