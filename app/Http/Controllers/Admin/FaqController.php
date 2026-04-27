<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage_faqs']);
    }

    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('question', 'like', '%' . $request->search . '%')
                  ->orWhere('answer', 'like', '%' . $request->search . '%');
            });
        }

        $faqs = $query->orderBy('sort_order')->paginate(20);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|in:general,admission,application,payment,support',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        Faq::create([
            'school_id' => auth()->user()->school_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        toastr()->success(__('FAQ created successfully.'));
        return redirect()->route('admin.faqs.index');
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|in:general,admission,application,payment,support',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        toastr()->success(__('FAQ updated successfully.'));
        return redirect()->route('admin.faqs.index');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        toastr()->success(__('FAQ deleted successfully.'));
        return redirect()->route('admin.faqs.index');
    }

    public function toggle($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->update(['is_active' => !$faq->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $faq->is_active,
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer',
        ]);

        foreach ($request->items as $index => $id) {
            Faq::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
