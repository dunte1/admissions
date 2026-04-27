@extends('layouts.admin')

@section('title', 'AI Settings')

@push('styles')
<style>
    .hover-lift {
        transition: all 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('header', 'AI Settings')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-robot text-[#00008B]"></i>
                AI Assistant Settings
            </h1>
            <p class="text-sm text-gray-500 mt-1">Configure your school's AI-powered admission assistant</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $settings->enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                <i class="fas {{ $settings->enabled ? 'fa-check-circle' : 'fa-pause-circle' }} mr-1"></i>
                {{ $settings->enabled ? 'Active' : 'Disabled' }}
            </span>
        </div>
    </div>

    <div x-data="aiSettings()" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cog text-[#00008B]"></i>
                        AI Configuration
                    </h3>
                </div>
                <form @submit.prevent="saveSettings" class="p-6 space-y-5" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-robot mr-1 text-gray-400"></i>
                                AI Assistant Name
                            </label>
                            <input type="text" x-model="form.ai_name" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all"
                                placeholder="e.g., Eliana D">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-language mr-1 text-gray-400"></i>
                                Default Language
                            </label>
                            <select x-model="form.default_language" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all">
                                <option value="en">English</option>
                                <option value="sw">Swahili</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                                <option value="es">Spanish</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment mr-1 text-gray-400"></i>
                                Communication Tone
                            </label>
                            <select x-model="form.tone" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all">
                                <option value="professional">Professional</option>
                                <option value="friendly">Friendly</option>
                                <option value="formal">Formal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-palette mr-1 text-gray-400"></i>
                                Primary Color
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="form.primary_color" 
                                    class="w-12 h-11 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" x-model="form.primary_color" 
                                    class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg text-sm font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-palette mr-1 text-gray-400"></i>
                                Secondary Color
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="form.secondary_color" 
                                    class="w-12 h-11 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" x-model="form.secondary_color" 
                                    class="flex-1 px-3 py-2.5 border border-gray-300 rounded-lg text-sm font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-1 text-gray-400"></i>
                                Operating Mode
                            </label>
                            <select x-model="form.mode" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all">
                                <option value="hybrid">Hybrid (Sales + Support)</option>
                                <option value="sales">Sales Only</option>
                                <option value="support">Support Only</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-image mr-1 text-gray-400"></i>
                            AI Avatar
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!avatarPreview">
                                    <i class="fas fa-robot text-gray-400 text-2xl"></i>
                                </template>
                            </div>
                            <div>
                                <input type="file" @change="handleAvatarUpload" accept="image/*" 
                                    class="hidden" id="avatarInput">
                                <label for="avatarInput" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-upload mr-2"></i>Upload Image
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Recommended: 200x200px</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-message mr-1 text-gray-400"></i>
                            Welcome Message
                        </label>
                        <textarea x-model="form.welcome_message" rows="3" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all"
                            placeholder="Hello! How can I help you today?"></textarea>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <p class="font-medium text-gray-900">Enable AI Assistant</p>
                            <p class="text-sm text-gray-500">Allow AI to respond to student queries</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.enabled" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#00008B]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <p class="font-medium text-gray-900">Human Handoff</p>
                            <p class="text-sm text-gray-500">Escalate to human when requested</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.human_handoff_enabled" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#00008B]"></div>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-server mr-1 text-gray-400"></i>
                                AI Provider
                            </label>
                            <select x-model="form.ai_provider" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all">
                                <option value="openrouter">OpenRouter</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-brain mr-1 text-gray-400"></i>
                                AI Model
                            </label>
                            <select x-model="form.ai_model" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all">
                                <option value="mistralai/mistral-7b-instruct">Mistral 7B</option>
                                <option value="openai/gpt-3.5-turbo">GPT-3.5 Turbo</option>
                                <option value="anthropic/claude-3-haiku">Claude 3 Haiku</option>
                                <option value="google/gemini-pro">Gemini Pro</option>
                            </select>
                        </div>
                    </div>
                    

                    <button type="submit" 
                        :disabled="saving"
                        class="w-full py-3 bg-[#00008B] text-white rounded-lg font-medium hover:bg-blue-800 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <template x-if="saving">
                            <i class="fas fa-spinner fa-spin"></i>
                        </template>
                        <template x-if="!saving">
                            <i class="fas fa-save"></i>
                        </template>
                        <span x-text="saving ? 'Saving...' : 'Save Settings'"></span>
                    </button>
                </form>
            </div>

            <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-brain text-green-600"></i>
                            Knowledge Base
                        </h3>
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                            {{ $knowledge->total() }} items
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <form @submit.prevent="addKnowledge" class="mb-6 space-y-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="text" x-model="knowledgeForm.title" placeholder="Question or title" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <select x-model="knowledgeForm.type" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="faq">FAQ</option>
                                    <option value="document">Document</option>
                                    <option value="policy">Policy</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <textarea x-model="knowledgeForm.content" placeholder="Answer or content" rows="2" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>
                        <button type="submit" 
                            class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all flex items-center gap-2">
                            <i class="fas fa-plus"></i> Add Knowledge
                        </button>
                    </form>
                    
                    <div class="space-y-3">
                        <template x-if="knowledgeItems.length === 0">
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                <p>No knowledge items yet</p>
                                <p class="text-sm">Add FAQs and documents for the AI to reference</p>
                            </div>
                        </template>
                        <template x-for="item in knowledgeItems" :key="item.id">
                            <div class="flex items-start justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                            :class="{
                                                'bg-blue-100 text-blue-700': item.type === 'faq',
                                                'bg-purple-100 text-purple-700': item.type === 'document',
                                                'bg-orange-100 text-orange-700': item.type === 'policy'
                                            }"
                                            x-text="item.type.toUpperCase()"></span>
                                        <h4 class="font-medium text-gray-900" x-text="item.title"></h4>
                                    </div>
                                    <p class="text-sm text-gray-500" x-text="item.content.substring(0, 100) + '...'"></p>
                                </div>
                                <button @click="deleteKnowledge(item.id)" 
                                    class="ml-4 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    @if($knowledge->hasPages())
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        {{ $knowledge->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-code text-purple-600"></i>
                        Embed Widget
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-600">Add the AI chat widget to your school's website</p>
                    
                    <template x-if="!embedCode">
                        <button @click="generateEmbedCode" 
                            :disabled="generating"
                            class="w-full py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                            <template x-if="generating">
                                <i class="fas fa-spinner fa-spin"></i>
                            </template>
                            <template x-if="!generating">
                                <i class="fas fa-code"></i>
                            </template>
                            <span x-text="generating ? 'Generating...' : 'Generate Embed Code'"></span>
                        </button>
                    </template>
                    
                    <template x-if="embedCode">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="apiKey" readonly 
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono bg-gray-50">
                                    <button @click="regenerateKey" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-gray-100 rounded-lg" title="Regenerate">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Embed Script</label>
                                <textarea x-model="embedCode" readonly rows="5" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs font-mono bg-gray-50"></textarea>
                            </div>
                            <button @click="copyEmbedCode" 
                                class="w-full py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-copy"></i> Copy to Clipboard
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-spider text-orange-500"></i>
                        Web Crawler
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-600">Crawl your school website to automatically build knowledge</p>
                    
                    <form @submit.prevent="startCrawl" class="space-y-3">
                        <input type="url" x-model="crawlUrl" placeholder="https://yourschool.ac.ke" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <button type="submit" 
                            :disabled="crawling"
                            class="w-full py-3 bg-orange-500 text-white rounded-lg font-medium hover:bg-orange-600 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                            <template x-if="crawling">
                                <i class="fas fa-spinner fa-spin"></i>
                            </template>
                            <template x-if="!crawling">
                                <i class="fas fa-play"></i>
                            </template>
                            <span x-text="crawling ? 'Crawling...' : 'Start Crawl'"></span>
                        </button>
                    </form>
                    
                    <template x-if="crawlStatus">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-2 text-sm">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span x-text="crawlStatus"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-eye text-gray-600"></i>
                        Widget Preview
                    </h3>
                </div>
                <div class="p-6">
                    <div class="bg-gray-100 rounded-xl p-4 h-48 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-3 mx-auto overflow-hidden"
                                :style="'background: ' + form.primary_color + ';'">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!avatarPreview">
                                    <i class="fas fa-robot text-white text-2xl"></i>
                                </template>
                            </div>
                            <p class="font-semibold text-gray-900" x-text="form.ai_name || 'AI Assistant'"></p>
                            <p class="text-xs text-gray-500 mt-1">Click to chat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function aiSettings() {
    return {
        saving: false,
        generating: false,
        crawling: false,
        embedCode: null,
        apiKey: null,
        crawlUrl: '',
        crawlStatus: null,
        avatarPreview: '{{ $settings->avatar ? asset("storage/" . $settings->avatar) : '' }}',
        form: {
            ai_name: '{{ $settings->ai_name ?? 'AI Assistant' }}',
            tone: '{{ $settings->tone ?? 'professional' }}',
            primary_color: '{{ $settings->primary_color ?? '#7C3AED' }}',
            secondary_color: '{{ $settings->secondary_color ?? '#10B981' }}',
            welcome_message: '{{ $settings->welcome_message ?? '' }}',
            mode: '{{ $settings->mode ?? 'hybrid' }}',
            enabled: {{ $settings->enabled ? 'true' : 'false' }},
            human_handoff_enabled: {{ $settings->human_handoff_enabled ? 'true' : 'false' }},
            default_language: '{{ $settings->default_language ?? 'en' }}',
            escalation_phrases: @json($settings->escalation_phrases ?? ['human', 'speak to person']),
            allowed_domains: @json($settings->allowed_domains ?? []),
            ai_provider: '{{ $settings->ai_provider ?? 'openrouter' }}',
            ai_model: '{{ $settings->ai_model ?? 'mistralai/mistral-7b-instruct' }}',
            rate_limit_per_minute: {{ $settings->rate_limit_per_minute ?? 10 }},
            max_tokens: {{ $settings->max_tokens ?? 500 }},
            temperature: {{ $settings->temperature ?? 0.7 }}
        },
        knowledgeForm: {
            title: '',
            type: 'faq',
            content: ''
        },
        knowledgeItems: @json($knowledge->items()),
        
        handleAvatarUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.avatarPreview = e.target.result;
                };
                reader.readAsDataURL(file);
                this.form.avatar = file;
            }
        },

        saveSettings() {
            var self = this;
            self.saving = true;
            
            var formData = new FormData();
            formData.append('ai_name', self.form.ai_name || 'AI Assistant');
            formData.append('tone', self.form.tone || 'professional');
            formData.append('primary_color', self.form.primary_color || '#7C3AED');
            formData.append('secondary_color', self.form.secondary_color || '#10B981');
            formData.append('welcome_message', self.form.welcome_message || '');
            formData.append('mode', self.form.mode || 'hybrid');
            formData.append('enabled', self.form.enabled ? '1' : '0');
            formData.append('human_handoff_enabled', self.form.human_handoff_enabled ? '1' : '0');
            formData.append('default_language', self.form.default_language || 'en');
            formData.append('escalation_phrases', JSON.stringify(self.form.escalation_phrases || ['human', 'speak to person', 'talk to admin']));
            formData.append('allowed_domains', JSON.stringify(self.form.allowed_domains || []));
            formData.append('ai_provider', self.form.ai_provider || 'openrouter');
            formData.append('ai_model', self.form.ai_model || 'mistralai/mistral-7b-instruct');
            formData.append('rate_limit_per_minute', self.form.rate_limit_per_minute || 10);
            formData.append('max_tokens', self.form.max_tokens || 500);
            formData.append('temperature', self.form.temperature || 0.7);
            
            if (self.form.avatar) {
                formData.append('avatar', self.form.avatar);
            }
            
            fetch('{{ route("admin.ai-settings.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(function(response) { 
                return response.json().catch(() => ({ success: false, message: 'Server error' })); 
            })
            .then(function(data) {
                self.saving = false;
                if (data.success) {
                    alert(data.message);
                    if (data.avatar_url) {
                        self.avatarPreview = data.avatar_url;
                    }
                    self.form.avatar = null;
                } else {
                    alert(data.message || 'Failed to save settings');
                }
            })
            .catch(function(error) {
                self.saving = false;
                alert('Failed to save settings: ' + error.message);
            })
            .finally(function() {
                self.saving = false;
            });
        },
        
        async addKnowledge() {
            try {
                const response = await fetch('{{ route("admin.ai-settings.knowledge.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: new URLSearchParams(this.knowledgeForm)
                });
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                toastr.error('Failed to add knowledge');
            }
        },
        
        async deleteKnowledge(id) {
            if (!confirm('Delete this knowledge item?')) return;
            try {
                const response = await fetch(`/admin/ai-settings/knowledge/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                toastr.error('Failed to delete knowledge');
            }
        },
        
        async generateEmbedCode() {
            this.generating = true;
            try {
                const response = await fetch('{{ route("admin.ai-settings.embed-code") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                if (data.success) {
                    this.embedCode = data.embed_code;
                    this.apiKey = data.api_key;
                }
            } catch (error) {
                toastr.error('Failed to generate embed code');
            }
            this.generating = false;
        },
        
        async regenerateKey() {
            try {
                const response = await fetch('{{ route("admin.ai-settings.regenerate-key") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                if (data.success) {
                    this.apiKey = data.api_key;
                    toastr.success('API key regenerated');
                }
            } catch (error) {
                toastr.error('Failed to regenerate key');
            }
        },
        
        copyEmbedCode() {
            navigator.clipboard.writeText(this.embedCode);
            toastr.success('Copied to clipboard!');
        },
        
        async startCrawl() {
            this.crawling = true;
            this.crawlStatus = null;
            try {
                const response = await fetch('{{ route("admin.ai-settings.crawl") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: new URLSearchParams({ url: this.crawlUrl })
                });
                const data = await response.json();
                if (data.success) {
                    this.crawlStatus = 'Crawl job started successfully!';
                    toastr.success('Crawl job started');
                }
            } catch (error) {
                toastr.error('Failed to start crawl');
            }
            this.crawling = false;
        }
    }
}
</script>
@endpush
@endsection