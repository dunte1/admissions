<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\Messaging\ConversationService;
use Illuminate\Support\Facades\Auth;

class ChatDropdown extends Component
{
    public $conversations;
    public $unreadCount;

    public function __construct(?ConversationService $conversationService = null)
    {
        if (Auth::check() && $conversationService) {
            $user = Auth::user();
            $this->conversations = $conversationService->getConversationsForUser($user, $user->school_id)->take(5);
            $this->unreadCount = $conversationService->getUnreadCount($user->id);
        } else {
            $this->conversations = collect();
            $this->unreadCount = 0;
        }
    }

    public function render()
    {
        return view('components.chat-dropdown');
    }
}
