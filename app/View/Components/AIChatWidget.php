<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AISettings;

class AIChatWidget extends Component
{
    public string $mode = 'sales';
    public string $primaryColor = '#7C3AED';
    public string $aiName = 'AI Assistant';
    public string $secondaryColor = '#10B981';
    public string $welcomeMessage = 'Hello! How can I help you today?';
    public ?string $avatarUrl = null;
    public bool $isEnabled = false;

    public function __construct(?string $mode = null)
    {
        $isLandingPage = !Auth::check();
        
        $this->mode = $mode ?? ($isLandingPage ? 'sales' : 'system');
        
        $schoolId = Auth::check() ? Auth::user()->school_id : null;
        
        if ($isLandingPage) {
            $settings = AISettings::getGlobalSettings();
            $this->isEnabled = $settings?->landing_enabled ?? true;
            $this->aiName = $settings?->landing_ai_name ?? $settings?->ai_name ?? 'Eliana D';
            $this->welcomeMessage = $settings?->landing_welcome_message ?? $settings?->welcome_message ?? 'Hello! How can I help you today?';
            $this->mode = $settings?->landing_mode ?? 'sales';
        } else {
            $settings = $schoolId ? AISettings::getForSchool($schoolId) : null;
            $this->isEnabled = $settings?->enabled ?? true;
            $this->aiName = $settings?->ai_name ?? 'AI Assistant';
            $this->welcomeMessage = $settings?->welcome_message ?? 'Hello! How can I help you today?';
        }
        
        $this->primaryColor = $settings?->primary_color ?? system_setting('primary_color', '#7C3AED');
        $this->secondaryColor = $settings?->secondary_color ?? '#10B981';
        $this->avatarUrl = $settings?->avatar ? asset('storage/' . $settings->avatar) : null;
    }

    public function render()
    {
        return view('components.ai-chat-widget');
    }
}
