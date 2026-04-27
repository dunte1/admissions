@extends('layouts.super-admin')

@section('header', 'Create Broadcast')
@section('breadcrumb', 'Compose a new notification')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-6">
                <a href="{{ route('super-admin.broadcast') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-purple-600">
                <i class="fas fa-arrow-left mr-2"></i> Back to Broadcasts
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('super-admin.broadcast.store') }}" method="POST" id="broadcastForm">
            @csrf

            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-pen mr-2 text-purple-600"></i> Message Composer
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required maxlength="255" value="{{ old('title') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="e.g., System Maintenance Notice">
                            @error('title')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea name="message" rows="6" required maxlength="5000"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Enter your notification message...">{{ old('message') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Available variables: &#123;&#123;name&#125;&#125;, &#123;&#123;first_name&#125;&#125;, &#123;&#123;email&#125;&#125;, &#123;&#123;school_name&#125;&#125;, &#123;&#123;date&#125;&#125;, &#123;&#123;year&#125;&#125;
                            </p>
                            @error('message')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Channel <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                                    <input type="radio" name="type" value="in_app" checked
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                        {{ old('type', 'in_app') === 'in_app' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">
                                        <i class="fas fa-bell text-purple-600 mr-1"></i> In-App
                                    </span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                                    <input type="radio" name="type" value="email"
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                        {{ old('type') === 'email' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">
                                        <i class="fas fa-envelope text-blue-600 mr-1"></i> Email
                                    </span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                                    <input type="radio" name="type" value="sms"
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                        {{ old('type') === 'sms' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">
                                        <i class="fas fa-sms text-green-600 mr-1"></i> SMS
                                    </span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                                    <input type="radio" name="type" value="all"
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                        {{ old('type') === 'all' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">
                                        <i class="fas fa-globe text-gray-600 mr-1"></i> All
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-crosshairs mr-2 text-purple-600"></i> Targeting
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Schools <span class="text-red-500">*</span></label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="targeting[schools]" value="all" checked
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                        id="schools_all">
                                    <span class="ml-2 text-sm font-medium">All Schools</span>
                                    <span class="ml-auto text-xs text-gray-500">{{ $schools->count() }} schools</span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="targeting[schools]" value="specific"
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                        id="schools_specific">
                                    <span class="ml-2 text-sm font-medium">Specific Schools</span>
                                </label>
                            </div>
                            <div id="schoolSelect" class="hidden mt-3">
                                <select name="targeting[schools_list][]" multiple
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple schools</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
                            <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                                @foreach($roles as $role)
                                    <label class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer text-sm">
                                        <input type="checkbox" name="targeting[roles][]" value="{{ $role }}"
                                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mr-2">
                                        {{ ucfirst($role) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application Status</label>
                            <div class="grid grid-cols-3 md:grid-cols-5 gap-2">
                                @foreach($applicationStatuses as $status)
                                    <label class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer text-sm">
                                        <input type="checkbox" name="targeting[application_status][]" value="{{ $status }}"
                                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mr-2">
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock mr-2 text-purple-600"></i> Schedule
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">When to Send <span class="text-red-500">*</span></label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="schedule_type" value="now" checked
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                        id="schedule_now">
                                    <span class="ml-2 text-sm font-medium">Send Immediately</span>
                                    <span class="ml-auto text-xs text-gray-500">Process in background</span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" name="schedule_type" value="schedule"
                                        class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500"
                                        id="schedule_later">
                                    <span class="ml-2 text-sm font-medium">Schedule for Later</span>
                                </label>
                            </div>
                        </div>

                        <div id="scheduleDateTime" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time</label>
                            <input type="datetime-local" name="scheduled_at"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                            @error('scheduled_at')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-flask mr-2 text-purple-600"></i> Test Mode
                    </h3>

                    <div class="space-y-4">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="is_test" value="1"
                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                id="test_mode">
                            <span class="ml-2 text-sm font-medium">Send as Test</span>
                            <span class="ml-auto text-xs text-gray-500">Send to test email only</span>
                        </label>

                        <div id="testEmailField" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Test Email Address</label>
                            <input type="email" name="test_email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                                placeholder="test@example.com"
                                value="{{ auth()->user()->email }}">
                            @error('test_email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t flex items-center justify-end space-x-4">
                <a href="{{ route('super-admin.broadcast') }}" class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>
                    <span id="submitBtnText">Create Broadcast</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('input[name="targeting[schools]"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const schoolSelect = document.getElementById('schoolSelect');
            if (this.value === 'specific') {
                schoolSelect.classList.remove('hidden');
            } else {
                schoolSelect.classList.add('hidden');
            }
        });
    });

    document.querySelectorAll('input[name="schedule_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const scheduleDateTime = document.getElementById('scheduleDateTime');
            const submitBtnText = document.getElementById('submitBtnText');
            if (this.value === 'schedule') {
                scheduleDateTime.classList.remove('hidden');
                submitBtnText.textContent = 'Schedule Broadcast';
            } else {
                scheduleDateTime.classList.add('hidden');
                submitBtnText.textContent = 'Create Broadcast';
            }
        });
    });

    document.getElementById('test_mode').addEventListener('change', function() {
        const testEmailField = document.getElementById('testEmailField');
        if (this.checked) {
            testEmailField.classList.remove('hidden');
        } else {
            testEmailField.classList.add('hidden');
        }
    });

    document.getElementById('schools_specific').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('schoolSelect').classList.remove('hidden');
        }
    });

    document.getElementById('schools_all').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('schoolSelect').classList.add('hidden');
        }
    });

    document.getElementById('broadcastForm').addEventListener('submit', function(e) {
        const schoolsSpecific = document.getElementById('schools_specific').checked;
        if (schoolsSpecific) {
            const selected = document.querySelector('select[name="targeting[schools_list][]"]').selectedOptions;
            if (selected.length === 0) {
                e.preventDefault();
                alert('Please select at least one school.');
                return;
            }
        }
    });
</script>
@endpush
@endsection
