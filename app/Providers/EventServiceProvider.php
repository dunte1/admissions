<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\Conversation;
use App\Models\Message;
use App\Observers\ApplicationObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
    ];

    public function boot(): void
    {
        parent::boot();

        Application::observe(ApplicationObserver::class);

        Broadcast::channel('user.{userId}', function ($user, $userId) {
            return $user->id === (int) $userId;
        });

        Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
            $conversation = Conversation::find($conversationId);
            return $conversation && $conversation->isParticipant($user->id);
        });

        Broadcast::channel('school.{schoolId}', function ($user, $schoolId) {
            return $user->school_id === (int) $schoolId;
        });
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
