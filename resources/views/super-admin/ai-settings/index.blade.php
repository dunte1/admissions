@extends('layouts.super-admin')

@section('title', 'AI Settings - ' . system_setting('system_name', 'Admission Portal'))

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
            <p class="text-sm text-gray-500 mt-1">Configure global AI assistant settings</p>
        </div>
    </div>
<div x-data="aiSettings()" class="space-y-6">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cog text-[#00008B]"></i>
                        General Configuration
                    </h3>
                </div>
                <form @submit.prevent="saveSettings" class="p-6 space-y-5">
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
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key mr-1 text-gray-400"></i>
                            OpenRouter API Key
                        </label>
                        <div class="relative">
                            <input :type="showApiKey ? 'text' : 'password'" x-model="form.openrouter_api_key" 
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all"
                                placeholder="sk-or-v1-...">
                            <button type="button" @click="showApiKey = !showApiKey" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i :class="showApiKey ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Get your API key from <a href="https://openrouter.ai/keys" target="_blank" class="text-blue-600 hover:underline">openrouter.ai</a></p>
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tachometer-alt mr-1 text-gray-400"></i>
                                Rate Limit (per min)
                            </label>
                            <input type="number" x-model="form.rate_limit_per_minute" min="1" max="60"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-1 text-gray-400"></i>
                                Max Tokens
                            </label>
                            <input type="number" x-model="form.max_tokens" min="100" max="4000"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-thermometer-half mr-1 text-gray-400"></i>
                                Temperature
                            </label>
                            <input type="number" x-model="form.temperature" min="0" max="2" step="0.1"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] transition-all">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-4">
                            <i class="fas fa-globe text-green-600"></i>
                            Landing Page AI Settings
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">Configure the AI assistant for the public landing page</p>
                        
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200 mb-4">
                            <div>
                                <p class="font-medium text-gray-900">Enable Landing Page AI</p>
                                <p class="text-sm text-gray-500">Show AI chat widget on the landing page</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.landing_enabled" class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:ring-4 peer-focus:ring-green-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-robot mr-1 text-gray-400"></i>
                                    Landing AI Name
                                </label>
                                <input type="text" x-model="form.landing_ai_name" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                                    placeholder="e.g., Eliana D">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-layer-group mr-1 text-gray-400"></i>
                                    Landing Mode
                                </label>
                                <select x-model="form.landing_mode" 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                                    <option value="sales">Sales Mode (Lead Capture)</option>
                                    <option value="support">Support Mode</option>
                                    <option value="hybrid">Hybrid Mode</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-message mr-1 text-gray-400"></i>
                                Landing Welcome Message
                            </label>
                            <textarea x-model="form.landing_welcome_message" rows="2" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all"
                                placeholder="Hello! How can I help you today?"></textarea>
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
                            <p class="text-sm text-gray-500">Allow AI to respond to user queries</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.enabled" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#00008B]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div>
                            <p class="font-medium text-gray-900">Human Handoff</p>
                            <p class="text-sm text-gray-500">Allow users to request human support</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" x-model="form.human_handoff_enabled" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#00008B]"></div>
                        </label>
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
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle text-orange-500"></i>
                        Escalation Phrases
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Words or phrases that trigger human handoff</p>
                    <div id="escalationPhrases" class="space-y-2">
                        <template x-for="(phrase, index) in form.escalation_phrases" :key="index">
                            <div class="flex items-center gap-2">
                                <input type="text" x-model="form.escalation_phrases[index]" 
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <button type="button" @click="removePhrase(index)" class="text-red-500 hover:text-red-700 p-2">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addPhrase()" class="mt-3 text-sm text-[#00008B] hover:underline font-medium">
                        <i class="fas fa-plus mr-1"></i>Add Phrase
                    </button>
                </div>
            </div>

            <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-globe text-blue-500"></i>
                        Embed Domain Restrictions
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Allowed domains for embed widget (leave empty to allow all)</p>
                    <div id="allowedDomains" class="space-y-2">
                        <template x-for="(domain, index) in form.allowed_domains" :key="index">
                            <div class="flex items-center gap-2">
                                <input type="text" x-model="form.allowed_domains[index]" 
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                    placeholder="e.g., example.com">
                                <button type="button" @click="removeDomain(index)" class="text-red-500 hover:text-red-700 p-2">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addDomain()" class="mt-3 text-sm text-[#00008B] hover:underline font-medium">
                        <i class="fas fa-plus mr-1"></i>Add Domain
                    </button>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-green-600"></i>
                        Quick Stats
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm">Total Chats</span>
                        <span class="font-semibold text-gray-900">{{ number_format($stats['total_chats']) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm">Messages</span>
                        <span class="font-semibold text-gray-900">{{ number_format($stats['total_messages']) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm">Escalations</span>
                        <span class="font-semibold text-red-600">{{ number_format($stats['total_escalations']) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600 text-sm">Conversions</span>
                        <span class="font-semibold text-green-600">{{ number_format($stats['conversions']) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-gray-600 text-sm">Active Schools</span>
                        <span class="font-semibold text-gray-900">{{ $stats['active_schools'] }}</span>
                    </div>
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
                    <div class="bg-gray-100 rounded-xl p-4 h-40 flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-2 overflow-hidden"
                                :style="'background: ' + form.primary_color + ';'">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!avatarPreview">
                                    <i class="fas fa-robot text-white text-xl"></i>
                                </template>
                            </div>
                            <p class="font-semibold text-gray-900 text-sm" x-text="form.ai_name || 'AI Assistant'"></p>
                            <p class="text-xs text-gray-500 mt-1">AI Assistant</p>
                        </div>
                    </div>
                    <div class="mt-4 bg-white rounded-lg p-3 text-sm text-gray-700 border border-gray-100">
                        <p x-text="form.welcome_message || 'Hello! I\'m your admission assistant.'"></p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <a href="{{ route('super-admin.ai-settings.analytics') }}" 
                    class="w-full py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-chart-line"></i>
                    Analytics
                </a>
                <a href="{{ route('super-admin.ai-settings.knowledge') }}" 
                    class="w-full py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-brain"></i>
                    Knowledge Base
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function aiSettings() {
    return {
        saving: false,
        showApiKey: false,
        avatarPreview: '{{ $settings->avatar ? asset("storage/" . $settings->avatar) : '' }}',
        form: {
            ai_name: '{{ $settings->ai_name }}',
            tone: '{{ $settings->tone }}',
            primary_color: '{{ $settings->primary_color }}',
            secondary_color: '{{ $settings->secondary_color }}',
            welcome_message: '{{ $settings->welcome_message }}',
            enabled: {{ $settings->enabled ? 'true' : 'false' }},
            human_handoff_enabled: {{ $settings->human_handoff_enabled ? 'true' : 'false' }},
            escalation_phrases: @json($settings->escalation_phrases ?? []),
            allowed_domains: @json($settings->allowed_domains ?? []),
            default_language: '{{ $settings->default_language ?? 'en' }}',
            openrouter_api_key: '{{ $settings->openrouter_api_key ?? '' }}',
            ai_provider: '{{ $settings->ai_provider ?? 'openrouter' }}',
            ai_model: '{{ $settings->ai_model ?? 'mistralai/mistral-7b-instruct' }}',
            rate_limit_per_minute: {{ $settings->rate_limit_per_minute ?? 10 }},
            max_tokens: {{ $settings->max_tokens ?? 500 }},
            temperature: {{ $settings->temperature ?? 0.7 }},
            landing_ai_name: '{{ $settings->landing_ai_name ?? 'Eliana D' }}',
            landing_mode: '{{ $settings->landing_mode ?? 'sales' }}',
            landing_enabled: {{ $settings->landing_enabled ? 'true' : 'false' }},
            landing_welcome_message: '{{ $settings->landing_welcome_message ?? '' }}'
        },

        addPhrase() {
            this.form.escalation_phrases.push('');
        },

        removePhrase(index) {
            this.form.escalation_phrases.splice(index, 1);
        },

        addDomain() {
            this.form.allowed_domains.push('');
        },

        removeDomain(index) {
            this.form.allowed_domains.splice(index, 1);
        },

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
            formData.append('ai_name', self.form.ai_name);
            formData.append('tone', self.form.tone);
            formData.append('primary_color', self.form.primary_color);
            formData.append('secondary_color', self.form.secondary_color);
            formData.append('welcome_message', self.form.welcome_message);
            formData.append('enabled', self.form.enabled ? '1' : '0');
            formData.append('human_handoff_enabled', self.form.human_handoff_enabled ? '1' : '0');
            formData.append('escalation_phrases', JSON.stringify(self.form.escalation_phrases || []));
            formData.append('allowed_domains', JSON.stringify(self.form.allowed_domains || []));
            formData.append('default_language', self.form.default_language || 'en');
            formData.append('openrouter_api_key', self.form.openrouter_api_key || '');
            formData.append('ai_provider', self.form.ai_provider || 'openrouter');
            formData.append('ai_model', self.form.ai_model || 'mistralai/mistral-7b-instruct');
            formData.append('rate_limit_per_minute', self.form.rate_limit_per_minute || 10);
            formData.append('max_tokens', self.form.max_tokens || 500);
            formData.append('temperature', self.form.temperature || 0.7);
            formData.append('landing_ai_name', self.form.landing_ai_name || 'Eliana D');
            formData.append('landing_mode', self.form.landing_mode || 'sales');
            formData.append('landing_enabled', self.form.landing_enabled ? '1' : '0');
            formData.append('landing_welcome_message', self.form.landing_welcome_message || '');
            
            if (self.form.avatar) {
                formData.append('avatar', self.form.avatar);
            }
            
            fetch('{{ route("super-admin.ai-settings.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
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
                alert('Failed to save settings');
            })
            .finally(function() {
                self.saving = false;
            });
        }
    }
}
</script>
@endpush
@endsection