<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Models\AIConversation;
use App\Models\AIMessage;
use App\Models\AIAnalytics;
use App\Models\AIAdmissionFlow;
use App\Models\AISettings;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AIController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $key = $this->throttleKey();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'success' => false,
                'error' => 'Too many requests. Please wait.',
                'reply' => "I've received too many requests recently. Please wait a moment before trying again.",
            ], 429);
        }

        RateLimiter::hit($key, 60);

        $schoolId = $this->getSchoolId();
        $mode = $request->input('mode', Auth::check() ? 'system' : 'sales');
        
        $this->aiService->setSchoolContext($schoolId);
        
        if ($request->has('language')) {
            $this->aiService->setLanguage($request->input('language'));
        }
        
        $context = $this->buildContext($request, $mode, $schoolId);
        $context['language'] = $request->input('language', 'en');

        $result = $this->aiService->chat(
            $request->input('message'),
            $context,
            $this->getSessionId()
        );

        $this->trackAnalytics($schoolId, 'message_sent', [
            'mode' => $mode,
            'success' => $result['success'],
            'lead_captured' => $result['lead_captured'] ?? false,
        ]);

        if ($request->input('start_conversation')) {
            $conversation = $this->startConversation($schoolId, $mode, $request->input('message'));
            $this->trackAnalytics($schoolId, 'chat_started', ['conversation_id' => $conversation->id ?? null]);
            
            if (isset($result['lead_captured']) && $result['lead_captured']) {
                $this->trackAnalytics($schoolId, 'lead_captured', [
                    'conversation_id' => $conversation->id,
                ]);
            }
        }

        if (isset($result['escalate']) && $result['escalate']) {
            $this->handleEscalation($schoolId, $request->input('message'), $result['reply']);
            $this->trackAnalytics($schoolId, 'escalation', ['reason' => 'human_request']);
        }

        return response()->json($result);
    }

    public function embedChat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'api_key' => 'required|string',
        ]);

        $settings = AISettings::where('api_key', $request->input('api_key'))->first();
        
        if (!$settings || !$settings->enabled || !$settings->embed_enabled) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API key or embed disabled.',
                'reply' => 'Chat widget is not available.',
            ], 403);
        }

        $domain = $request->header('Referer');
        if ($domain && !$settings->isDomainAllowed($domain)) {
            return response()->json([
                'success' => false,
                'error' => 'Domain not allowed.',
                'reply' => 'Chat widget is not available on this domain.',
            ], 403);
        }

        if (RateLimiter::tooManyAttempts('embed_' . $settings->school_id, 20)) {
            return response()->json([
                'success' => false,
                'error' => 'Rate limit exceeded.',
                'reply' => 'Too many requests. Please wait.',
            ], 429);
        }

        RateLimiter::hit('embed_' . $settings->school_id, 60);

        $context = [
            'mode' => 'embed',
            'school_id' => $settings->school_id,
            'language' => $request->input('language', 'en'),
        ];

        $this->aiService->setSchoolContext($settings->school_id);
        
        if ($request->has('language')) {
            $this->aiService->setLanguage($request->input('language'));
        }
        
        $result = $this->aiService->chat($request->input('message'), $context);

        return response()->json([
            'success' => $result['success'],
            'reply' => $result['reply'],
            'escalate' => $result['escalate'] ?? false,
            'lead_captured' => $result['lead_captured'] ?? false,
            'ai_name' => $settings->ai_name,
        ]);
    }

    public function greeting(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $mode = $request->input('mode', Auth::check() ? 'system' : 'sales');
        
        $this->aiService->setSchoolContext($schoolId);
        
        return response()->json([
            'success' => true,
            'greeting' => $this->aiService->getGreeting($mode),
        ]);
    }

    public function clearHistory(): JsonResponse
    {
        $this->aiService->clearHistory($this->getSessionId());
        
        return response()->json([
            'success' => true,
            'message' => 'Chat history cleared',
        ]);
    }

    public function history(): JsonResponse
    {
        $sessionId = $this->getSessionId();
        
        if (Auth::check()) {
            $history = AIMessage::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();
        } else {
            $conversation = AIConversation::where('session_id', $sessionId)->first();
            if (!$conversation) {
                return response()->json(['success' => true, 'history' => []]);
            }
            $history = $conversation->messages()->orderBy('created_at', 'asc')->get();
        }
        
        return response()->json([
            'success' => true,
            'history' => $history->map(fn($h) => [
                'id' => $h->id,
                'role' => $h->role,
                'content' => $h->content,
                'created_at' => $h->created_at->toISOString(),
            ]),
        ]);
    }

    public function admissionFlow(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:start,collect,next,complete,abandon',
        ]);

        $schoolId = $this->getSchoolId();
        $sessionId = $this->getSessionId();

        return match($request->input('action')) {
            'start' => $this->startAdmissionFlow($schoolId, $sessionId),
            'collect' => $this->collectFlowData($schoolId, $sessionId, $request),
            'next' => $this->advanceFlow($schoolId, $sessionId, $request),
            'complete' => $this->completeFlow($schoolId, $sessionId),
            'abandon' => $this->abandonFlow($schoolId, $sessionId),
            default => response()->json(['success' => false, 'error' => 'Invalid action']),
        };
    }

    protected function startAdmissionFlow($schoolId, $sessionId): JsonResponse
    {
        $flow = AIAdmissionFlow::create([
            'school_id' => $schoolId,
            'session_id' => $sessionId,
            'user_id' => Auth::id(),
            'status' => 'in_progress',
            'current_step' => 1,
            'collected_data' => [],
        ]);

        $this->trackAnalytics($schoolId, 'admission_flow_started', [
            'flow_id' => $flow->id,
        ]);

        return response()->json([
            'success' => true,
            'flow_id' => $flow->id,
            'step' => 1,
            'step_name' => $flow->step_name,
            'message' => $this->getStepPrompt(1),
        ]);
    }

    protected function collectFlowData($schoolId, $sessionId, Request $request): JsonResponse
    {
        $flow = AIAdmissionFlow::where('session_id', $sessionId)->first();
        
        if (!$flow || $flow->status !== 'in_progress') {
            return response()->json(['success' => false, 'error' => 'No active flow']);
        }

        $field = $request->input('field');
        $value = $request->input('value');
        
        $flow->collectData($field, $value);

        return response()->json([
            'success' => true,
            'message' => 'Data collected. Say "next" to proceed or ask me anything.',
        ]);
    }

    protected function advanceFlow($schoolId, $sessionId, Request $request): JsonResponse
    {
        $flow = AIAdmissionFlow::where('session_id', $sessionId)->first();
        
        if (!$flow || $flow->status !== 'in_progress') {
            return response()->json(['success' => false, 'error' => 'No active flow']);
        }

        $nextStep = $flow->current_step + 1;
        
        if ($nextStep > 8) {
            return $this->completeFlow($schoolId, $sessionId);
        }

        $flow->advanceToStep($nextStep);

        return response()->json([
            'success' => true,
            'step' => $nextStep,
            'step_name' => $flow->step_name,
            'message' => $this->getStepPrompt($nextStep),
            'fields' => $this->getStepFields($nextStep),
        ]);
    }

    protected function completeFlow($schoolId, $sessionId): JsonResponse
    {
        $flow = AIAdmissionFlow::where('session_id', $sessionId)->first();
        
        if (!$flow) {
            return response()->json(['success' => false, 'error' => 'No active flow']);
        }

        $collected = $flow->collected_data ?? [];
        
        $this->trackAnalytics($schoolId, 'conversion', [
            'flow_id' => $flow->id,
            'steps_completed' => $flow->current_step,
        ]);

        $flow->complete();

        return response()->json([
            'success' => true,
            'message' => 'Great! Your information has been collected. You can now proceed to submit your formal application through the system.',
            'redirect' => route('student.application.create'),
        ]);
    }

    protected function abandonFlow($schoolId, $sessionId): JsonResponse
    {
        $flow = AIAdmissionFlow::where('session_id', $sessionId)->first();
        
        if ($flow) {
            $flow->abandon();
            $this->trackAnalytics($schoolId, 'admission_flow_abandoned', [
                'flow_id' => $flow->id,
                'steps_completed' => $flow->current_step,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'No problem! You can always start your application later.',
        ]);
    }

    protected function getStepPrompt(int $step): string
    {
        return match($step) {
            1 => "Let's start your admission journey! What program or course are you interested in applying for?",
            2 => "Great choice! Could you tell me more about your academic background and qualifications?",
            3 => "Now, let's collect your personal information. What's your full name?",
            4 => "Please provide your academic background - previous schools attended and grades.",
            5 => "What documents do you have available? (ID, certificates, photos, etc.)",
            6 => "Ready to discuss application fees? Our fee is [amount]. How would you like to pay?",
            7 => "Let's review your information before submission.",
            8 => "Everything looks good! Ready to submit your application?",
            default => "Let's continue with your application.",
        };
    }

    protected function getStepFields(int $step): array
    {
        return match($step) {
            1 => ['program_interest', 'level'],
            2 => ['qualification', 'grades'],
            3 => ['full_name', 'email', 'phone', 'date_of_birth'],
            4 => ['previous_schools', 'grades', 'certificates'],
            5 => ['document_types'],
            6 => ['payment_method'],
            7 => [],
            8 => [],
            default => [],
        };
    }

    protected function buildContext(Request $request, string $mode, ?int $schoolId): array
    {
        $context = [
            'mode' => $mode,
            'school_id' => $schoolId,
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $context['user_id'] = $user->id;
            $context['user_role'] = $user->getRoleNames()->first() ?? 'user';
            $context['user_name'] = $user->fullName();
            $context['current_route'] = $request->route()->getName() ?? request()->path();
            $context['module_name'] = $this->getModuleName($request);
            
            if ($user->school) {
                $context['school_name'] = $user->school->name;
                $context['school_id'] = $user->school_id;
            }
        }

        return $context;
    }

    protected function getModuleName(Request $request): string
    {
        $routeName = $request->route()->getName() ?? '';
        
        $moduleMap = [
            'admin.dashboard' => 'Dashboard',
            'admin.applications' => 'Applications',
            'admin.users' => 'User Management',
            'admin.programs' => 'Program Management',
            'admin.settings' => 'Settings',
            'student.dashboard' => 'Student Dashboard',
            'student.application' => 'Application',
            'super-admin.dashboard' => 'Super Admin Dashboard',
        ];

        foreach ($moduleMap as $pattern => $module) {
            if (str_starts_with($routeName, $pattern)) {
                return $module;
            }
        }

        return 'General';
    }

    protected function getSessionId(): string
    {
        if (Auth::check()) {
            return 'user_' . Auth::id();
        }
        
        return session()->getId();
    }

    protected function getSchoolId(): ?int
    {
        if (Auth::check() && Auth::user()->school_id) {
            return Auth::user()->school_id;
        }
        
        return null;
    }

    protected function throttleKey(): string
    {
        return 'ai_chat_' . (Auth::id() ?? request()->ip());
    }

    protected function startConversation($schoolId, $mode, $initialMessage): AIConversation
    {
        return AIConversation::create([
            'school_id' => $schoolId,
            'user_id' => Auth::id(),
            'session_id' => $this->getSessionId(),
            'mode' => $mode,
            'initial_message' => $initialMessage,
            'status' => 'active',
            'message_count' => 1,
        ]);
    }

    protected function handleEscalation($schoolId, string $message, string $reply): void
    {
        $conversation = AIConversation::where('session_id', $this->getSessionId())
            ->where('status', 'active')
            ->first();

        if ($conversation) {
            $admin = \App\Models\User::role('admin')
                ->where('school_id', $schoolId)
                ->first();

            if ($admin) {
                $conversation->escalateTo($admin->id, 'User requested human assistance');
                
                $admin->notify(new \App\Notifications\EscalationNotification($conversation));
            }
        }

        $this->trackAnalytics($schoolId, 'escalation', [
            'session_id' => $this->getSessionId(),
            'message' => $message,
        ]);
    }

    protected function trackAnalytics($schoolId, string $eventType, array $metadata = []): void
    {
        try {
            AIAnalytics::track(
                $eventType,
                $schoolId,
                Auth::id(),
                $metadata,
                $this->getSessionId()
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to track AI analytics: ' . $e->getMessage());
        }
    }
}
