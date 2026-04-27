@extends('layouts.admin')

@section('title', 'Create Broadcast')
@section('header', 'Create Broadcast')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <a href="{{ route('admin.broadcast.index') }}" class="inline-flex items-center text-gray-600 hover:text-purple-600 mb-4">
                <i class="fas fa-arrow-left mr-2"></i> Back to Broadcasts
            </a>
            <h2 class="text-xl font-semibold text-gray-900">Create New Broadcast</h2>
            <p class="text-gray-500 text-sm mt-1">Send notifications to users within your school</p>
        </div>

        <form action="{{ route('admin.broadcast.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Broadcast title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Notification Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="in_app" {{ old('type') === 'in_app' ? 'selected' : '' }}>In-App Only</option>
                        <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email Only</option>
                        <option value="all" {{ old('type') === 'all' ? 'selected' : '' }}>In-App + Email</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="template_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Use Template <span class="text-gray-400">(optional)</span>
                </label>
                <select name="template_id" id="template_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">-- Select a template --</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }} ({{ ucfirst($template->type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea name="message" id="message" rows="6" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                          placeholder="Enter your broadcast message... Use {{name}} for personalized greeting">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    Available variables: {{ '{{name}}' }}, {{ '{{first_name}}' }}, {{ '{{last_name}}' }}, {{ '{{email}}' }}, {{ '{{school_name}}' }}, {{ '{{date}}' }}
                </p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Target Users <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($roles as $role)
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="targeting[roles][]" value="{{ $role }}" 
                                   class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                   {{ in_array($role, old('targeting.roles', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 capitalize">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500">Leave unchecked to send to all users in your school</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Target by Application Status
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($applicationStatuses as $status)
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="targeting[application_status][]" value="{{ $status }}"
                                   class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                   {{ in_array($status, old('targeting.application_status', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    When to Send
                </label>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="radio" name="schedule_type" value="now" checked
                               class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                               {{ old('schedule_type', 'now') === 'now' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Send Now</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="schedule_type" value="schedule"
                               class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                               {{ old('schedule_type') === 'schedule' ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">Schedule for Later</span>
                    </label>
                </div>
            </div>

            <div id="schedule-fields" class="mb-6 {{ old('schedule_type') === 'schedule' ? '' : 'hidden' }}">
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">
                    Schedule Date & Time
                </label>
                <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       value="{{ old('scheduled_at') }}">
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.broadcast.index') }}" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-md">
                    <i class="fas fa-paper-plane mr-2"></i> Send Broadcast
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('input[name="schedule_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const scheduleFields = document.getElementById('schedule-fields');
        if (this.value === 'schedule') {
            scheduleFields.classList.remove('hidden');
        } else {
            scheduleFields.classList.add('hidden');
        }
    });
});

document.getElementById('template_id').addEventListener('change', function() {
    const templateId = this.value;
    if (templateId) {
        const templates = @json($templates);
        const template = templates.find(t => t.id == templateId);
        if (template) {
            if (template.subject && !document.getElementById('title').value) {
                document.getElementById('title').value = template.subject;
            }
            if (template.content && !document.getElementById('message').value) {
                document.getElementById('message').value = template.content;
            }
        }
    }
});
</script>
@endpush
