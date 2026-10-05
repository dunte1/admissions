<?php

namespace App\Services;

use App\Models\AIKnowledge;
use App\Models\AISettings;
use App\Models\AILead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected int $maxTokens;
    protected float $temperature;
    protected ?AISettings $settings;
    protected ?int $schoolId;
    protected string $language;

    protected array $languagePrompts = [
        'en' => '',
        'sw' => 'Respond in Swahili language. ',
        'fr' => 'Respond in French language. ',
        'de' => 'Respond in German language. ',
        'es' => 'Respond in Spanish language. ',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key', env('OPENROUTER_API_KEY', ''));
        $this->baseUrl = config('services.openrouter.api_url', 'https://openrouter.ai/api/v1');
        $this->model = config('services.openrouter.model', 'mistralai/mistral-7b-instruct');
        $this->maxTokens = config('services.openrouter.max_tokens', 500);
        $this->temperature = (float) config('services.openrouter.temperature', 0.7);
        $this->language = 'en';
    }

    public function setSchoolContext(?int $schoolId): self
    {
        $this->schoolId = $schoolId;
        $this->settings = $schoolId 
            ? AISettings::getForSchool($schoolId)
            : AISettings::getGlobalSettings();
        
        $this->language = $this->settings?->default_language ?? 'en';

        if ($this->settings) {
            $this->apiKey = $this->settings->openrouter_api_key 
                ?? config('services.openrouter.api_key', env('OPENROUTER_API_KEY', ''));
            $this->model = $this->settings->ai_model ?? $this->model;
            $this->maxTokens = $this->settings->max_tokens ?? $this->maxTokens;
            $this->temperature = (float) ($this->settings->temperature ?? $this->temperature);
        }
        
        return $this;
    }

    public function setLanguage(string $language): self
    {
        if (isset($this->languagePrompts[$language])) {
            $this->language = $language;
        }
        return $this;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function isEnabled(): bool
    {
        return $this->settings?->enabled ?? false;
    }

    public function chat(string $message, array $context = [], ?string $sessionId = null): array
    {
        $this->setSchoolContext($context['school_id'] ?? null);

        if (isset($context['language']) && isset($this->languagePrompts[$context['language']])) {
            $this->language = $context['language'];
        }

        if (!$this->isConfigured()) {
            return $this->errorResponse('AI service is not configured. Please set OPENROUTER_API_KEY.');
        }

        if (!$this->isEnabled()) {
            return $this->errorResponse('AI assistant is currently disabled.');
        }

        $leadData = $this->extractLeadData($message, $context);
        if ($leadData) {
            $this->saveLead($leadData, $sessionId);
        }

        $systemPrompt = $this->buildSystemPrompt($context);
        $cacheKey = $this->getCacheKey($sessionId);
        $history = $this->getHistory($cacheKey);

        $knowledgeContext = $this->getKnowledgeContext($message);
        if ($knowledgeContext) {
            $systemPrompt = $knowledgeContext . "\n\n" . $systemPrompt;
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ...$history,
            ['role' => 'user', 'content' => $message]
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url', 'https://example.com'),
                    'X-Title' => config('app.name', 'Admission Portal'),
                ])
                ->post($this->baseUrl . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => $this->maxTokens,
                    'temperature' => $this->temperature,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'I apologize, I could not generate a response.';
                $reply = $this->cleanResponse($reply);

                $this->saveToHistory($cacheKey, $message, $reply);
                $this->saveToDatabase($message, $reply, $context, $data['usage'] ?? null);

                $escalate = $this->checkForEscalation($message);
                if ($escalate) {
                    return [
                        'success' => true,
                        'reply' => $reply,
                        'escalate' => true,
                        'escalation_message' => 'I understand you need human assistance. Let me connect you with our support team.',
                    ];
                }

                $responseData = [
                    'success' => true,
                    'reply' => $reply,
                    'usage' => $data['usage'] ?? null,
                ];

                if ($leadData) {
                    $responseData['lead_captured'] = true;
                    $responseData['lead_message'] = 'Thank you! Our team will contact you soon.';
                }

                return $responseData;
            }

            Log::error('OpenRouter API Error', ['status' => $response->status(), 'body' => $response->body()]);
            return $this->errorResponse('AI service temporarily unavailable.');

        } catch (\Exception $e) {
            Log::error('AI Service Exception', ['message' => $e->getMessage()]);
            return $this->errorResponse('I apologize, I\'m experiencing technical difficulties.');
        }
    }

    protected function extractLeadData(string $message, array $context): ?array
    {
        $mode = $context['mode'] ?? 'sales';
        
        if (!in_array($mode, ['sales', 'hybrid', 'embed'])) {
            return null;
        }

        $messageLower = strtolower($message);
        
        $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        $phonePattern = '/(\+?[\d\s\-\(\)]{10,})/';
        
        $email = null;
        $phone = null;
        $name = null;
        
        if (preg_match($emailPattern, $message, $matches)) {
            $email = $matches[0];
        }
        
        if (preg_match($phonePattern, $message, $matches)) {
            $phone = preg_replace('/[^\d+]/', '', $matches[0]);
            if (strlen($phone) < 10) {
                $phone = null;
            }
        }
        
        $leadKeywords = ['my name is', 'i am', 'i\'m', 'name is', 'called'];
        foreach ($leadKeywords as $keyword) {
            if (str_contains($messageLower, $keyword)) {
                $parts = explode($keyword, $messageLower);
                if (count($parts) > 1) {
                    $namePart = trim($parts[1]);
                    $namePart = preg_replace('/[^\w\s]/', '', $namePart);
                    $nameParts = explode(' ', $namePart);
                    $name = ucwords(implode(' ', array_slice($nameParts, 0, 2)));
                    break;
                }
            }
        }

        if ($email || $phone) {
            return [
                'school_id' => $this->schoolId,
                'name' => $name ?? 'Unknown',
                'email' => $email,
                'phone' => $phone,
                'source' => $mode === 'embed' ? 'embed_chat' : 'ai_chat',
            ];
        }

        return null;
    }

    protected function saveLead(array $leadData, ?string $sessionId): void
    {
        try {
            $existingLead = AILead::forSchool($leadData['school_id'])
                ->where('email', $leadData['email'])
                ->first();

            if (!$existingLead) {
                AILead::create($leadData);
                Log::info('Lead captured from AI chat', ['email' => $leadData['email']]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to save lead: ' . $e->getMessage());
        }
    }

    protected function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => $message,
            'reply' => $message
        ];
    }

    protected function getKnowledgeContext(string $message): ?string
    {
        if (!$this->schoolId) {
            return null;
        }

        $keywords = $this->extractKeywords($message);
        
        $knowledgeItems = AIKnowledge::active()
            ->forSchool($this->schoolId)
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('title', 'like', "%{$keyword}%")
                          ->orWhere('content', 'like', "%{$keyword}%");
                }
            })
            ->limit(5)
            ->get();

        if ($knowledgeItems->isEmpty()) {
            return null;
        }

        $context = "RELEVANT KNOWLEDGE BASE INFORMATION:\n";
        foreach ($knowledgeItems as $item) {
            $context .= "- {$item->title}: {$item->content}\n";
        }
        
        return $context;
    }

    protected function extractKeywords(string $text): array
    {
        $stopWords = ['the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'shall', 'can', 'need', 'dare', 'ought', 'used', 'to', 'of', 'in', 'for', 'on', 'with', 'at', 'by', 'from', 'as', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'between', 'under', 'again', 'further', 'then', 'once', 'here', 'there', 'when', 'where', 'why', 'how', 'all', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than', 'too', 'very', 'just', 'what', 'who', 'which', 'and', 'but', 'or', 'because', 'until', 'while'];
        
        $words = preg_split('/\s+/', strtolower($text));
        $keywords = array_filter($words, fn($word) => strlen($word) > 3 && !in_array($word, $stopWords));
        
        return array_values(array_slice(array_unique($keywords), 0, 10));
    }

    protected function checkForEscalation(string $message): bool
    {
        if (!$this->settings?->human_handoff_enabled) {
            return false;
        }

        $phrases = $this->settings->escalation_phrases ?? [];
        $messageLower = strtolower($message);

        foreach ($phrases as $phrase) {
            if (str_contains($messageLower, strtolower($phrase))) {
                return true;
            }
        }

        return false;
    }

    protected function buildSystemPrompt(array $context): string
    {
        $mode = $context['mode'] ?? 'sales';
        
        $aiName = $this->settings?->ai_name ?? 'Eliana D';
        $tone = $this->settings?->tone ?? 'professional';
        $welcomeMessage = $this->settings?->welcome_message ?? '';
        
        $languageInstruction = $this->languagePrompts[$this->language] ?? '';
        
        $systemName = system_setting('system_name', 'Admission Portal');
        $tagline = system_setting('tagline', 'Streamlining admissions for institutions');
        $contactEmail = system_setting('contact_email', 'info@example.com');
        $contactPhone = system_setting('contact_phone', '+254700000000');

        $toneInstructions = match($tone) {
            'friendly' => "Use a warm, friendly, and conversational tone. Add emojis occasionally. Be enthusiastic.",
            'formal' => "Use a formal, professional tone. Be concise and precise. Avoid casual language.",
            default => "Use a professional yet approachable tone. Be helpful and friendly while maintaining professionalism."
        };

        $basePrompt = "You are {$aiName}, a helpful and professional AI assistant for {$systemName}. ";
        $basePrompt .= $languageInstruction;
        $basePrompt .= $toneInstructions . " ";
        $basePrompt .= "Current system info: {$tagline}. ";
        $basePrompt .= "Contact: {$contactEmail} | {$contactPhone}. ";

        if ($welcomeMessage) {
            $basePrompt .= "\nYour welcome message to users: {$welcomeMessage}\n";
        }

        if ($mode === 'sales' || $mode === 'hybrid') {
            $basePrompt .= "\n\nMODE: SALES/ADMISSIONS AGENT ";
            $basePrompt .= "You are speaking to a potential student/parent visiting the landing page or using the embed widget. ";
            $basePrompt .= "Your goal is to:\n";
            $basePrompt .= "1. Warmly greet and engage visitors\n";
            $basePrompt .= "2. Answer questions about programs, admissions, and the institution\n";
            $basePrompt .= "3. Guide them towards creating an account and applying\n";
            $basePrompt .= "4. Highlight key benefits and features of the platform\n";
            $basePrompt .= "5. Assist with the admission flow when appropriate\n";
            $basePrompt .= "6. Encourage action (signing up, asking questions, starting application)\n";
            $basePrompt .= "7. Capture lead information (name, email, phone) when users express interest\n";
            $basePrompt .= "8. Mention demo requests or trial signup when appropriate\n\n";
            $basePrompt .= "Do NOT discuss any dashboard features, admin functions, or system internals. ";
            $basePrompt .= "Keep responses concise and friendly.";
        } 

        if ($mode === 'system' || $mode === 'hybrid') {
            $userRole = $context['user_role'] ?? 'unknown';
            $currentRoute = $context['current_route'] ?? 'dashboard';
            $moduleName = $context['module_name'] ?? 'general';
            $schoolName = $context['school_name'] ?? $systemName;

            $basePrompt .= "\n\nMODE: SUPPORT ASSISTANT ";
            $basePrompt .= "You are helping a logged-in user with the following profile:\n";
            $basePrompt .= "- Role: {$userRole}\n";
            $basePrompt .= "- Current Location: {$currentRoute}\n";
            $basePrompt .= "- Module: {$moduleName}\n";
            $basePrompt .= "- School/Institution: {$schoolName}\n\n";
            $basePrompt .= "Your goals:\n";
            $basePrompt .= "1. Help users navigate and use the system effectively\n";
            $basePrompt .= "2. Explain features and functionalities relevant to their current context\n";
            $basePrompt .= "3. Provide guidance on application processes, document uploads, payments, etc.\n";
            $basePrompt .= "4. Assist with troubleshooting common issues\n";
            $basePrompt .= "5. Be patient and thorough in your explanations\n\n";
            $basePrompt .= "IMPORTANT: Never reveal sensitive information, user data, system credentials, ";
            $basePrompt .= "or internal database structures. ";
            $basePrompt .= "Do NOT execute any system commands or modify any data. ";
            $basePrompt .= "If asked about other users' data, politely decline. ";
            $basePrompt .= "Keep responses focused and helpful.";
        }

        $basePrompt .= "\n\nResponse guidelines:\n";
        $basePrompt .= "- Keep responses under 3 paragraphs\n";
        $basePrompt .= "- Use simple, clear language\n";
        $basePrompt .= "- Be friendly and professional\n";
        $basePrompt .= "- If you don't know something, say so and offer to help find the answer\n";
        $basePrompt .= "- Always maintain user privacy and system security";

        return $basePrompt;
    }

    protected function cleanResponse(string $reply): string
    {
        $reply = trim($reply);
        $reply = preg_replace('/^["\']|["\']$/i', '', $reply);
        $reply = preg_replace('/^(Eliana|Eliana D)[,:]\s*/i', '', $reply);
        $reply = preg_replace('/^\*\*Eliana D:\*\*\s*/i', '', $reply);
        $reply = preg_replace('/^Assistant[,:]\s*/i', '', $reply);

        return $reply;
    }

    protected function getCacheKey(?string $sessionId): string
    {
        $prefix = $this->schoolId ? "school_{$this->schoolId}_" : '';
        return $prefix . 'ai_chat_history_' . ($sessionId ?? session()->getId());
    }

    protected function getHistory(string $cacheKey, int $maxMessages = 10): array
    {
        $history = Cache::get($cacheKey, []);
        return array_slice($history, -$maxMessages);
    }

    protected function saveToHistory(string $cacheKey, string $userMessage, string $aiReply): void
    {
        $history = Cache::get($cacheKey, []);

        $history[] = ['role' => 'user', 'content' => $userMessage];
        $history[] = ['role' => 'assistant', 'content' => $aiReply];

        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }

        Cache::put($cacheKey, $history, now()->addHours(24));
    }

    protected function saveToDatabase(string $message, string $reply, array $context, ?array $usage): void
    {
        try {
            \App\Models\ChatHistory::saveConversation(
                userId: $context['user_id'] ?? auth()->id(),
                sessionId: $context['session_id'] ?? session()->getId(),
                mode: $context['mode'] ?? 'sales',
                message: $message,
                response: $reply,
                context: $context,
                tokensUsed: $usage['total_tokens'] ?? 0
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to save chat history to database', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function clearHistory(?string $sessionId = null): void
    {
        $cacheKey = $this->getCacheKey($sessionId);
        Cache::forget($cacheKey);
    }

    public function getGreeting(string $mode = 'sales'): string
    {
        $isLanding = !$this->schoolId && $mode === 'sales';
        
        if ($isLanding && $this->settings?->landing_welcome_message) {
            return $this->settings->landing_welcome_message;
        }
        
        if ($this->settings?->welcome_message) {
            return $this->settings->welcome_message;
        }

        if ($mode === 'sales') {
            return "Hello! I'm Eliana D, your admission assistant. How can I help you today? Feel free to ask me about our programs, admission requirements, or how to get started with your application!";
        }

        return "Hello! I'm Eliana D. I'm here to help you navigate the system. What would you like assistance with today?";
    }
}
