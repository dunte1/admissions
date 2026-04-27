<?php

namespace App\Http\Controllers\Messaging;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use App\Services\Messaging\ConversationService;
use App\Services\Messaging\MessageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    protected ConversationService $conversationService;
    protected MessageService $messageService;

    public function __construct(ConversationService $conversationService, MessageService $messageService)
    {
        $this->conversationService = $conversationService;
        $this->messageService = $messageService;
    }

    public function index(Request $request): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        $user = auth()->user();
        $conversations = $this->conversationService->getConversationsForUser($user, $user->school_id);
        $totalUnread = $this->conversationService->getUnreadCount($user->id);

        if ($request->expectsJson()) {
            return response()->json([
                'conversations' => $conversations->map(function ($conv) {
                    return [
                        'id' => $conv->id,
                        'subject' => $conv->subject,
                        'initiator' => [
                            'id' => $conv->initiator->id,
                            'name' => $conv->initiator->fullName(),
                            'avatar' => $conv->initiator->avatar_url,
                        ],
                        'recipient' => [
                            'id' => $conv->recipient->id,
                            'name' => $conv->recipient->fullName(),
                            'avatar' => $conv->recipient->avatar_url,
                        ],
                        'last_message' => $conv->messages->first() ? [
                            'content' => $conv->messages->first()->content,
                            'created_at' => $conv->messages->first()->created_at->toIso8601String(),
                        ] : null,
                        'last_message_at' => $conv->last_message_at?->toIso8601String(),
                        'unread_count' => $conv->unread_count,
                        'status' => $conv->status,
                    ];
                }),
                'total_unread' => $totalUnread,
            ]);
        }

        return view('messaging.index', compact('conversations', 'totalUnread'));
    }

    public function show(Conversation $conversation): \Illuminate\View\View|JsonResponse
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user->id)) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Conversation not found'], 404);
            }
            abort(404);
        }

        $conversation = $this->conversationService->getConversation($conversation->id, $user->id);
        $messages = $this->messageService->getMessages($conversation, $user->id);

        $this->conversationService->markAsRead($conversation, $user->id);

        if (request()->expectsJson()) {
            return response()->json([
                'conversation' => [
                    'id' => $conversation->id,
                    'subject' => $conversation->subject,
                    'initiator' => [
                        'id' => $conversation->initiator->id,
                        'name' => $conversation->initiator->fullName(),
                        'avatar' => $conversation->initiator->avatar_url,
                    ],
                    'recipient' => [
                        'id' => $conversation->recipient->id,
                        'name' => $conversation->recipient->fullName(),
                        'avatar' => $conversation->recipient->avatar_url,
                    ],
                    'status' => $conversation->status,
                ],
                'messages' => $messages->map(function ($msg) {
                    return [
                        'id' => $msg->id,
                        'sender_id' => $msg->sender_id,
                        'sender_name' => $msg->sender->fullName(),
                        'sender_avatar' => $msg->sender->avatar_url,
                        'content' => $msg->content,
                        'type' => $msg->type,
                        'attachment_url' => $msg->attachment_url,
                        'attachment_name' => $msg->attachment_name,
                        'attachment_size' => $msg->attachment_size,
                        'attachment_mime_type' => $msg->attachment_mime_type,
                        'is_edited' => $msg->is_edited,
                        'read_at' => $msg->read_at?->toIso8601String(),
                        'created_at' => $msg->created_at->toIso8601String(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'last_page' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ],
            ]);
        }

        return view('messaging.show', compact('conversation', 'messages'));
    }

    public function create(Request $request): \Illuminate\View\View|JsonResponse
    {
        $user = auth()->user();

        $users = User::where('id', '!=', $user->id)
            ->when($user->school_id, function ($query) use ($user) {
                $query->where('school_id', $user->school_id);
            })
            ->when($user->isSuperAdmin(), function ($query) {
            })
            ->when($user->isStudent(), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('school_id', $user->school_id)
                      ->where(function ($q2) {
                          $q2->where('role', '!=', 'student');
                      });
                });
            })
            ->orderBy('first_name')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'users' => $users->map(function ($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->fullName(),
                        'email' => $u->email,
                        'avatar' => $u->avatar_url,
                        'role' => $u->getRoleNames()->first(),
                    ];
                }),
            ]);
        }

        return view('messaging.create', compact('users'));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $recipient = User::findOrFail($request->recipient_id);

        if (!$this->conversationService->canMessage($user, $recipient)) {
            return response()->json(['error' => 'You cannot send messages to this user'], 403);
        }

        $conversation = $this->conversationService->createConversation(
            $user,
            $recipient,
            $request->subject,
            $user->school_id
        );

        $message = $this->messageService->sendMessage(
            $conversation,
            $user,
            $request->message
        );

        return response()->json([
            'conversation_id' => $conversation->id,
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'created_at' => $message->created_at->toIso8601String(),
            ],
        ], 201);
    }

    public function startConversation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();
        $recipient = User::findOrFail($request->user_id);

        if (!$this->conversationService->canMessage($user, $recipient)) {
            return response()->json(['error' => 'You cannot send messages to this user'], 403);
        }

        $conversation = $this->conversationService->getOrCreateConversation($user, $recipient, $user->school_id);

        return response()->json([
            'conversation_id' => $conversation->id,
        ]);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $user = auth()->user();
        $query = $request->get('q', '');

        $users = User::where('id', '!=', $user->id)
            ->when($user->school_id, function ($q) use ($user) {
                $q->where('school_id', $user->school_id);
            })
            ->when($user->isStudent(), function ($q) use ($user) {
                $q->where(function ($q2) {
                    $q2->where('role', '!=', 'student');
                });
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q2) use ($query) {
                    $q2->where('first_name', 'like', "%{$query}%")
                       ->orWhere('last_name', 'like', "%{$query}%")
                       ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->orderBy('first_name')
            ->limit(10)
            ->get()
            ->map(function ($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->fullName(),
                    'email' => $u->email,
                    'avatar' => $u->avatar_url,
                    'role' => $u->getRoleNames()->first(),
                ];
            });

        return response()->json($users);
    }

    public function markAsRead(Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->conversationService->markAsRead($conversation, $user->id);

        return response()->json(['success' => true]);
    }

    public function typing(Request $request, Conversation $conversation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = auth()->user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->conversationService->setTyping($conversation, $user, $request->is_typing);

        return response()->json(['success' => true]);
    }

    public function archive(Conversation $conversation): JsonResponse
    {
        $user = auth()->user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->conversationService->archiveConversation($conversation, $user->id);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount(): JsonResponse
    {
        $user = auth()->user();
        $count = $this->conversationService->getUnreadCount($user->id);

        return response()->json(['unread_count' => $count]);
    }

    public function unreadCounts(): JsonResponse
    {
        $user = auth()->user();
        $conversations = $this->conversationService->getConversationsForUser($user, $user->school_id);

        $counts = $conversations->mapWithKeys(function ($conv) use ($user) {
            return [$conv->id => $conv->getUnreadCount($user->id)];
        });

        return response()->json(['counts' => $counts]);
    }
}
