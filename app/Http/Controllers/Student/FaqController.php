<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query()->where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('question', 'like', '%' . $request->search . '%')
                  ->orWhere('answer', 'like', '%' . $request->search . '%');
            });
        }

        $faqs = $query->orderBy('sort_order')->get();
        $categories = Faq::query()->where('is_active', true)
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('student.faqs.index', compact('faqs', 'categories'));
    }
}
