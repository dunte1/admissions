<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AISettings;
use App\Models\AIAnalytics;
use App\Models\AIKnowledge;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AISettingsController extends Controller
{
    public function index(): View
    {
        $settings = AISettings::getGlobalSettings();
        $stats = $this->getGlobalStats();
        $recentActivity = AIAnalytics::orderBy('created_at', 'desc')->limit(20)->get();
        
        return view('super-admin.ai-settings.index', compact('settings', 'stats', 'recentActivity'));
    }

    public function update(Request $request): JsonResponse
    {
        $settings = AISettings::getGlobalSettings();
        
        $data = $request->all();
        
        $data['enabled'] = $request->has('enabled') && $request->enabled == '1';
        $data['human_handoff_enabled'] = $request->has('human_handoff_enabled') && $request->human_handoff_enabled == '1';
        
        if ($request->has('escalation_phrases')) {
            $data['escalation_phrases'] = json_decode($request->escalation_phrases, true) ?? [];
        }
        
        if ($request->has('allowed_domains')) {
            $data['allowed_domains'] = json_decode($request->allowed_domains, true) ?? [];
        }

        $validated = [
            'ai_name' => $data['ai_name'] ?? 'Eliana D',
            'ai_icon' => $data['ai_icon'] ?? null,
            'primary_color' => $data['primary_color'] ?? '#7C3AED',
            'secondary_color' => $data['secondary_color'] ?? '#10B981',
            'welcome_message' => $data['welcome_message'] ?? '',
            'tone' => $data['tone'] ?? 'professional',
            'mode' => $data['mode'] ?? 'hybrid',
            'enabled' => $data['enabled'],
            'human_handoff_enabled' => $data['human_handoff_enabled'],
            'escalation_phrases' => $data['escalation_phrases'],
            'allowed_domains' => $data['allowed_domains'],
            'default_language' => $data['default_language'] ?? 'en',
            'openrouter_api_key' => $data['openrouter_api_key'] ?? null,
            'ai_provider' => $data['ai_provider'] ?? 'openrouter',
            'ai_model' => $data['ai_model'] ?? 'mistralai/mistral-7b-instruct',
            'rate_limit_per_minute' => (int) ($data['rate_limit_per_minute'] ?? 10),
            'max_tokens' => (int) ($data['max_tokens'] ?? 500),
            'temperature' => (float) ($data['temperature'] ?? 0.7),
            'landing_ai_name' => $data['landing_ai_name'] ?? 'Eliana D',
            'landing_mode' => $data['landing_mode'] ?? 'sales',
            'landing_enabled' => $data['landing_enabled'] ?? true,
            'landing_welcome_message' => $data['landing_welcome_message'] ?? '',
        ];

        if ($request->hasFile('avatar')) {
            if ($settings->avatar) {
                \Storage::disk('public')->delete($settings->avatar);
            }
            $path = $request->file('avatar')->store('ai-avatars', 'public');
            $validated['avatar'] = $path;
        }

        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Global AI settings updated successfully',
            'avatar_url' => $settings->avatar ? asset('storage/' . $settings->avatar) : null,
        ]);
    }

    public function knowledge(): View
    {
        $knowledge = AIKnowledge::orderBy('created_at', 'desc')->paginate(20);
        return view('super-admin.ai-settings.knowledge', compact('knowledge'));
    }

    public function storeKnowledge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:faq,document,policy',
        ]);

        AIKnowledge::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Knowledge item created successfully',
        ]);
    }

    public function getKnowledge(AIKnowledge $knowledge): JsonResponse
    {
        return response()->json($knowledge);
    }

    public function updateKnowledge(Request $request, AIKnowledge $knowledge): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:faq,document,policy',
            'is_active' => 'boolean',
        ]);

        $knowledge->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Knowledge item updated successfully',
        ]);
    }

    public function destroyKnowledge(AIKnowledge $knowledge): JsonResponse
    {
        $knowledge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Knowledge item deleted successfully',
        ]);
    }

    public function analytics(Request $request): View
    {
        $startDate = $request->get('start_date', now()->subDays(30));
        $endDate = $request->get('end_date', now());
        
        $stats = AIAnalytics::selectRaw('
            DATE(created_at) as date,
            event_type,
            COUNT(*) as count
        ')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date', 'event_type')
        ->orderBy('date', 'desc')
        ->get();

        $eventTypes = AIAnalytics::selectRaw('event_type, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('event_type')
            ->pluck('count', 'event_type');

        $recentActivity = AIAnalytics::orderBy('created_at', 'desc')->limit(20)->get();

        return view('super-admin.ai-settings.analytics', compact('stats', 'eventTypes', 'startDate', 'endDate', 'recentActivity'));
    }

    protected function getGlobalStats(): array
    {
        return [
            'total_chats' => AIAnalytics::where('event_type', 'chat_started')->count(),
            'total_messages' => AIAnalytics::where('event_type', 'message_sent')->count(),
            'total_escalations' => AIAnalytics::where('event_type', 'escalation')->count(),
            'conversions' => AIAnalytics::where('event_type', 'conversion')->count(),
            'active_schools' => AISettings::whereNotNull('school_id')->where('enabled', true)->count(),
        ];
    }
}
