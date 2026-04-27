<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_applications');

        $query = Document::withoutGlobalScopes()
            ->with(['application.student', 'application.program', 'verifier']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $documents = $query->latest()->paginate(25);

        return view('admin.documents.index', compact('documents'));
    }

    public function verify(Request $request, Document $document)
    {
        $this->authorize('view_applications');

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $document->verify(auth()->id(), $request->notes);

        AuditLog::log('verify_document', $document, null, [
            'document_type' => $document->type,
            'application_id' => $document->application_id,
        ]);

        toastr()->success('Document verified successfully.');
        return back();
    }

    public function reject(Request $request, Document $document)
    {
        $this->authorize('view_applications');

        $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $document->reject(auth()->id(), $request->reason, $request->notes);

        AuditLog::log('reject_document', $document, null, [
            'document_type' => $document->type,
            'application_id' => $document->application_id,
            'reason' => $request->reason,
        ]);

        toastr()->success('Document rejected. Applicant will be notified.');
        return back();
    }

    public function bulkVerify(Request $request)
    {
        $this->authorize('view_applications');

        $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $documents = Document::whereIn('id', $request->document_ids)
            ->where('status', 'pending')
            ->get();

        foreach ($documents as $document) {
            $document->verify(auth()->id(), $request->notes);
        }

        AuditLog::log('bulk_verify_documents', null, null, [
            'count' => $documents->count(),
        ]);

        toastr()->success("{$documents->count()} documents verified.");
        return back();
    }
}
