<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterTemplate;
use App\Models\LetterTemplateVersion;
use Illuminate\Http\Request;

class LetterTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_settings');
    }

    public function index()
    {
        $templates = LetterTemplate::with('creator', 'school')
            ->latest()
            ->paginate(20);

        return view('admin.letter-templates.index', compact('templates'));
    }

    public function create()
    {
        $types = LetterTemplate::getAvailableTypes();
        return view('admin.letter-templates.create', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(LetterTemplate::getAvailableTypes())),
            'header_content' => 'nullable|string',
            'body_content' => 'nullable|string',
            'footer_content' => 'nullable|string',
        ]);

        $template = LetterTemplate::create([
            'school_id' => school_id(),
            'name' => $request->name,
            'type' => $request->type,
            'header_content' => $request->header_content,
            'body_content' => $request->body_content,
            'footer_content' => $request->footer_content,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        $template->createVersion(
            $request->header_content ?? '',
            $request->body_content ?? '',
            $request->footer_content ?? '',
            'Initial version',
            auth()->id()
        );

        toastr()->success('Letter template created successfully.');
        return redirect()->route('admin.letter-templates.index');
    }

    public function show(LetterTemplate $letterTemplate)
    {
        $letterTemplate->load(['versions' => fn($q) => $q->orderByDesc('version'), 'creator', 'updater']);

        return view('admin.letter-templates.show', compact('letterTemplate'));
    }

    public function edit(LetterTemplate $letterTemplate)
    {
        $types = LetterTemplate::getAvailableTypes();
        return view('admin.letter-templates.edit', compact('letterTemplate', 'types'));
    }

    public function update(Request $request, LetterTemplate $letterTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(LetterTemplate::getAvailableTypes())),
            'header_content' => 'nullable|string',
            'body_content' => 'nullable|string',
            'footer_content' => 'nullable|string',
            'change_note' => 'nullable|string|max:500',
        ]);

        $oldContent = [
            'header' => $letterTemplate->header_content,
            'body' => $letterTemplate->body_content,
            'footer' => $letterTemplate->footer_content,
        ];

        $newContent = [
            'header' => $request->header_content ?? '',
            'body' => $request->body_content ?? '',
            'footer' => $request->footer_content ?? '',
        ];

        $letterTemplate->update([
            'name' => $request->name,
            'header_content' => $newContent['header'],
            'body_content' => $newContent['body'],
            'footer_content' => $newContent['footer'],
            'updated_by' => auth()->id(),
        ]);

        if ($oldContent !== $newContent) {
            $letterTemplate->createVersion(
                $newContent['header'],
                $newContent['body'],
                $newContent['footer'],
                $request->change_note ?? 'Updated template',
                auth()->id()
            );
        }

        toastr()->success('Letter template updated successfully.');
        return redirect()->route('admin.letter-templates.index');
    }

    public function destroy(LetterTemplate $letterTemplate)
    {
        $letterTemplate->delete();
        toastr()->success('Letter template deleted.');
        return back();
    }

    public function history(LetterTemplate $letterTemplate)
    {
        $versions = LetterTemplateVersion::where('letter_template_id', $letterTemplate->id)
            ->orderByDesc('version')
            ->paginate(20);

        return view('admin.letter-templates.history', compact('letterTemplate', 'versions'));
    }

    public function revert(LetterTemplate $letterTemplate, int $version)
    {
        if ($letterTemplate->revertTo($version)) {
            toastr()->success("Template reverted to version {$version}.");
        } else {
            toastr()->error('Version not found.');
        }

        return back();
    }

    public function activate(LetterTemplate $letterTemplate)
    {
        LetterTemplate::where('school_id', $letterTemplate->school_id)
            ->where('type', $letterTemplate->type)
            ->update(['is_active' => false]);

        $letterTemplate->update(['is_active' => true]);

        toastr()->success('Template activated.');
        return back();
    }

    public function preview(Request $request)
    {
        $type = $request->type ?? 'admission';
        $defaultContent = LetterTemplate::getDefaultContent($type);

        $template = LetterTemplate::where('type', $type)
            ->where('school_id', school_id())
            ->where('is_active', true)
            ->first();

        return response()->json([
            'header' => $template?->header_content ?? $defaultContent['header'],
            'body' => $template?->body_content ?? $defaultContent['body'],
            'footer' => $template?->footer_content ?? $defaultContent['footer'],
        ]);
    }

    public function generate(Request $request)
    {
        $type = $request->type ?? 'admission';
        $template = LetterTemplate::where('type', $type)
            ->where('school_id', school_id())
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return response()->json(['error' => 'No active template found'], 404);
        }

        $variables = [
            '{{student_name}}' => 'John Doe',
            '{{application_number}}' => 'APP20260001',
            '{{program}}' => 'Computer Science',
            '{{institution}}' => 'Sample University',
            '{{letter_number}}' => 'LET20260001',
            '{{issue_date}}' => now()->format('F d, Y'),
            '{{response_deadline}}' => now()->addMonth()->format('F d, Y'),
            '{{interview_date}}' => now()->addDays(7)->format('F d, Y g:i A'),
            '{{interview_venue}}' => 'Main Campus, Room 101',
            '{{address}}' => '123 University Road',
            '{{phone}}' => '+254 700 000 000',
            '{{email}}' => 'admissions@university.ac.ke',
        ];

        $preview = [
            'header' => str_replace(array_keys($variables), array_values($variables), $template->header_content),
            'body' => str_replace(array_keys($variables), array_values($variables), $template->body_content),
            'footer' => str_replace(array_keys($variables), array_values($variables), $template->footer_content),
        ];

        return response()->json($preview);
    }
}