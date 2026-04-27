<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $programs = Program::where('is_active', true)
            ->with('department')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('welcome', compact('programs'));
    }
}
