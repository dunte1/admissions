<?php
namespace App\Http\Controllers\Api\V1;use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class DocumentController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpeg,jpg,png|max:5120',
            'application_id' => 'nullable|exists:applications,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $documentType = $request->document_type;
        $path = $file->store('documents/' . date('Y/m'), 'public');

        $document = Document::create([
            'user_id' => $request->user()->id,
            'school_id' => $request->user()->school_id,
            'application_id' => $request->application_id,
            'document_type' => $documentType,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully',
            'data' => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'file_name' => $document->file_name,
                'file_size' => $document->file_size,
                'status' => $document->status,
            ],
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Document::where('user_id', $request->user()->id);

        if ($request->has('application_id')) {
            $query->where('application_id', $request->application_id);
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'file_name' => $doc->file_name,
                    'file_size' => $doc->file_size,
                    'status' => $doc->status,
                    'application_id' => $doc->application_id,
                    'created_at' => $doc->created_at,
                ];
            }),
        ]);
    }

    public function show(Document $document): JsonResponse
    {
        if ($document->user_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $document->id,
                'document_type' => $document->document_type,
                'file_name' => $document->file_name,
                'file_size' => $document->file_size,
                'status' => $document->status,
                'verified_at' => $document->verified_at,
                'created_at' => $document->created_at,
            ],
        ]);
    }

    public function download(Document $document): JsonResponse
    {
        if ($document->user_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        $url = Storage::disk('public')->url($document->file_path);

        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => $url,
                'file_name' => $document->file_name,
            ],
        ]);
    }

    public function destroy(Document $document): JsonResponse
    {
        if ($document->user_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($document->status === 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete verified document',
            ], 400);
        }

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully',
        ]);
    }
}