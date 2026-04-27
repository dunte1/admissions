<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AISettings;
use App\Models\AIKnowledge;
use App\Models\AICrawlJob;
use App\Jobs\ProcessWebCrawlJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AISettingsController extends Controller
{
    public function index(): View
    {
        $settings = AISettings::getForSchool(Auth::user()->school_id);
        $knowledge = AIKnowledge::forSchool(Auth::user()->school_id)->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.ai-settings.index', compact('settings', 'knowledge'));
    }

    public function update(Request $request): JsonResponse
    {
        $schoolId = Auth::user()->school_id;
        $settings = AISettings::getForSchool($schoolId);
        
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
            'ai_name' => $data['ai_name'] ?? 'AI Assistant',
            'primary_color' => $data['primary_color'] ?? '#7C3AED',
            'secondary_color' => $data['secondary_color'] ?? '#10B981',
            'welcome_message' => $data['welcome_message'] ?? '',
            'tone' => $data['tone'] ?? 'professional',
            'mode' => $data['mode'] ?? 'hybrid',
            'enabled' => isset($data['enabled']) ? ($data['enabled'] == '1') : true,
            'human_handoff_enabled' => isset($data['human_handoff_enabled']) ? ($data['human_handoff_enabled'] == '1') : true,
            'escalation_phrases' => is_array($data['escalation_phrases']) ? $data['escalation_phrases'] : json_decode($data['escalation_phrases'] ?? '[]', true) ?? ['human', 'speak to person'],
            'allowed_domains' => is_array($data['allowed_domains']) ? $data['allowed_domains'] : json_decode($data['allowed_domains'] ?? '[]', true) ?? [],
            'default_language' => $data['default_language'] ?? 'en',
            'ai_provider' => $data['ai_provider'] ?? 'openrouter',
            'ai_model' => $data['ai_model'] ?? 'mistralai/mistral-7b-instruct',
            'rate_limit_per_minute' => (int) ($data['rate_limit_per_minute'] ?? 10),
            'max_tokens' => (int) ($data['max_tokens'] ?? 500),
            'temperature' => (float) ($data['temperature'] ?? 0.7),
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
            'message' => 'AI settings updated successfully',
            'avatar_url' => $settings->avatar ? asset('storage/' . $settings->avatar) : null,
        ]);
    }

    public function generateEmbedCode(): JsonResponse
    {
        $settings = AISettings::getForSchool(Auth::user()->school_id);
        
        if (!$settings->api_key) {
            $settings->generateApiKey();
        }
        
        $script = $settings->generateEmbedScript();

        return response()->json([
            'success' => true,
            'embed_code' => $script,
            'api_key' => $settings->api_key,
        ]);
    }

    public function storeKnowledge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:faq,document,policy',
        ]);

        $validated['school_id'] = Auth::user()->school_id;
        AIKnowledge::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Knowledge item created',
        ]);
    }

    public function destroyKnowledge(AIKnowledge $knowledge): JsonResponse
    {
        if ($knowledge->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $knowledge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Knowledge item deleted',
        ]);
    }

    public function crawl(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $crawlJob = AICrawlJob::create([
            'school_id' => Auth::user()->school_id,
            'url' => $request->input('url'),
            'status' => 'pending',
        ]);

        ProcessWebCrawlJob::dispatch($crawlJob);

        return response()->json([
            'success' => true,
            'message' => 'Crawl job started',
            'job_id' => $crawlJob->id,
        ]);
    }

    public function crawlStatus(AICrawlJob $crawlJob): JsonResponse
    {
        if ($crawlJob->school_id !== Auth::user()->school_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $crawlJob->status,
            'pages_crawled' => $crawlJob->pages_crawled,
            'items_indexed' => $crawlJob->items_indexed,
            'error' => $crawlJob->error_message,
        ]);
    }

    public function regenerateApiKey(): JsonResponse
    {
        $settings = AISettings::getForSchool(Auth::user()->school_id);
        $newKey = $settings->generateApiKey();

        return response()->json([
            'success' => true,
            'api_key' => $newKey,
        ]);
    }
}
