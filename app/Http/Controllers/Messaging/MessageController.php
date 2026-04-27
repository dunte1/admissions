<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Messaging\MessageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index(Request $request, Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $this->messageService->getMessages($conversation, $user->id, $request->get('per_page', 50));

        return response()->json([
            'messages' => $messages->map(function ($msg) {
                return $this->formatMessage($msg);
            }),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required_without:attachment|string|max:5000',
            'attachment' => 'required_without:content|file|max:10240|mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $message = $this->messageService->sendMessage(
                $conversation,
                $user,
                $request->content ?? '',
                $request->hasFile('attachment') ? $request->file('attachment') : null
            );

            return response()->json([
                'message' => $this->formatMessage($message->load('sender')),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function loadMore(Request $request, Conversation $conversation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'before' => 'required|integer|exists:messages,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $this->messageService->getOlderMessages(
            $conversation,
            $user->id,
            $request->before,
            $request->get('limit', 20)
        );

        return response()->json([
            'messages' => $messages->map(function ($msg) {
                return $this->formatMessage($msg);
            }),
        ]);
    }

    public function update(Request $request, Message $message): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        try {
            $updatedMessage = $this->messageService->editMessage($message, $user, $request->content);

            return response()->json([
                'message' => $this->formatMessage($updatedMessage->load('sender')),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function destroy(Message $message): JsonResponse
    {
        $user = auth()->user();

        try {
            $this->messageService->deleteMessage($message, $user);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }

    public function markAsRead(Request $request, Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messageIds = $request->get('message_ids', []);

        $this->messageService->markMessagesAsRead($conversation, $user->id, $messageIds);

        return response()->json(['success' => true]);
    }

    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        if ($request->conversation_id) {
            $conversation = Conversation::find($request->conversation_id);
            if (!$conversation || !$conversation->isParticipant($user->id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $messages = $this->messageService->searchMessages(
            $user->id,
            $request->q,
            $request->conversation_id
        );

        return response()->json([
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'conversation_id' => $msg->conversation_id,
                    'conversation_subject' => $msg->conversation->subject,
                    'sender_name' => $msg->sender->fullName(),
                    'sender_avatar' => $msg->sender->avatar_url,
                    'content' => $msg->content,
                    'created_at' => $msg->created_at->toIso8601String(),
                ];
            }),
        ]);
    }

    protected function formatMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->fullName(),
            'sender_avatar' => $message->sender->avatar_url,
            'content' => $message->content,
            'type' => $message->type,
            'attachment_url' => $message->attachment_url,
            'attachment_name' => $message->attachment_name,
            'attachment_size' => $message->attachment_size,
            'attachment_mime_type' => $message->attachment_mime_type,
            'is_edited' => $message->is_edited,
            'is_image' => $message->is_image,
            'formatted_size' => $message->formatted_size,
            'read_at' => $message->read_at?->toIso8601String(),
            'delivered_at' => $message->delivered_at?->toIso8601String(),
            'created_at' => $message->created_at->toIso8601String(),
            'created_at_human' => $message->created_at->diffForHumans(),
        ];
    }
}
