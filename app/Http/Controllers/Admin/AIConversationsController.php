<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIConversation;
use App\Models\AIMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AIConversationsController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = AIConversation::forSchool(Auth::user()->school_id)
            ->with(['user', 'escalatedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.ai.conversations.index', compact('conversations'));
    }

    public function show(AIConversation $conversation): View
    {
        $this->authorizeConversation($conversation);
        
        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();
        
        return view('admin.ai.conversations.show', compact('conversation', 'messages'));
    }

    public function update(Request $request, AIConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);
        
        $conversation->update([
            'resolution_notes' => $request->input('resolution_notes'),
        ]);

        if ($request->input('action') === 'resolve') {
            $conversation->markResolved($request->input('resolution_notes'));
        }

        return response()->json(['success' => true, 'message' => 'Conversation updated']);
    }

    public function escalate(AIConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);
        
        $conversation->escalateTo(
            Auth::id(),
            'Re-escalated by admin'
        );

        return response()->json(['success' => true, 'message' => 'Conversation escalated']);
    }

    public function resolve(AIConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);
        
        $conversation->markResolved(request('resolution_notes'));

        return response()->json(['success' => true, 'message' => 'Conversation resolved']);
    }

    public function destroy(AIConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($conversation);
        
        $conversation->delete();

        return response()->json(['success' => true, 'message' => 'Conversation deleted']);
    }

    public function escalated(): View
    {
        $conversations = AIConversation::forSchool(Auth::user()->school_id)
            ->where('status', 'escalated')
            ->with(['user', 'escalatedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.ai.conversations.escalated', compact('conversations'));
    }

    protected function authorizeConversation(AIConversation $conversation): void
    {
        if ($conversation->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized');
        }
    }
}
