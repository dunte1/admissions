<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\School;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = Faq::withoutGlobalScopes()->with('school');

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        } elseif ($request->filled('search_school')) {
            $schoolIds = School::where('name', 'like', '%' . $request->search_school . '%')->pluck('id');
            $query->whereIn('school_id', $schoolIds);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('is_global')) {
            $query->whereNull('school_id');
        }

        $faqs = $query->orderBy('school_id')->orderBy('sort_order')->paginate(30);
        $schools = School::orderBy('name')->get();

        return view('super-admin.faqs.index', compact('faqs', 'schools'));
    }

    public function create(Request $request)
    {
        $schools = School::orderBy('name')->get();
        $schoolId = $request->school_id;

        return view('super-admin.faqs.create', compact('schools', 'schoolId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'nullable|exists:schools,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|in:general,admission,application,payment,support',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        Faq::create([
            'school_id' => $request->school_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('super-admin.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function edit($id)
    {
        $faq = Faq::withoutGlobalScopes()->findOrFail($id);
        $schools = School::orderBy('name')->get();
        return view('super-admin.faqs.edit', compact('faq', 'schools'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'school_id' => 'nullable|exists:schools,id',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|in:general,admission,application,payment,support',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $faq = Faq::withoutGlobalScopes()->findOrFail($id);
        $faq->update([
            'school_id' => $request->school_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('super-admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy($id)
    {
        $faq = Faq::withoutGlobalScopes()->findOrFail($id);
        $faq->delete();

        return redirect()->route('super-admin.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    public function toggle($id)
    {
        $faq = Faq::withoutGlobalScopes()->findOrFail($id);
        $faq->update(['is_active' => !$faq->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $faq->is_active,
        ]);
    }
}
