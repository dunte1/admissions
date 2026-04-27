<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications;
    public $unreadCount;
    public $notificationUrl;

    public function __construct(?string $url = null)
    {
        if (Auth::check()) {
            $this->notifications = Auth::user()->unreadNotifications()->take(10)->get();
            $this->unreadCount = Auth::user()->unreadNotifications()->count();
        } else {
            $this->notifications = collect();
            $this->unreadCount = 0;
        }
        
        $this->notificationUrl = $url ?? $this->getDefaultNotificationUrl();
    }

    protected function getDefaultNotificationUrl(): string
    {
        $user = Auth::user();
        
        if ($user->hasRole('super_admin')) {
            return route('notifications.index');
        }
        
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return route('notifications.index');
        }
        
        if ($user->isStudent()) {
            return route('student.notifications.index');
        }
        
        return route('notifications.index');
    }

    public function render()
    {
        return view('components.notification-dropdown');
    }
}
