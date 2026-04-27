@extends('layouts.admin')

@section('title', __('Create Letter Template'))

@section('header', __('Create Letter Template'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.letter-templates.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Create Letter Template') }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <form action="{{ route('admin.letter-templates.store') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Template Name') }} *</label>
                                <input type="text" name="name" id="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Letter Type') }} *</label>
                                <select name="type" id="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required onchange="loadDefaults()">
                                    <option value="">-- Select Type --</option>
                                    @foreach($types as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="header_content" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Header Content (HTML)') }}</label>
                            <textarea name="header_content" id="header_content" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="4" placeholder="<h1>{{institution}}</h1>"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Available variables: {{institution}}, {{address}}, {{phone}}, {{email}}, {{website}}</p>
                        </div>

                        <div>
                            <label for="body_content" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Body Content (HTML)') }}</label>
                            <textarea name="body_content" id="body_content" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="8" placeholder="<p>Dear {{student_name}},</p>"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Variables: {{student_name}}, {{application_number}}, {{program}}, {{letter_number}}, {{issue_date}}, {{response_deadline}}, {{interview_date}}, {{interview_venue}}</p>
                        </div>

                        <div>
                            <label for="footer_content" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Footer Content (HTML)') }}</label>
                            <textarea name="footer_content" id="footer_content" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="3" placeholder="<p>This is a system-generated document.</p>"></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="previewTemplate()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-eye mr-2"></i> Preview
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af]">
                                <i class="fas fa-save mr-2"></i> Save Template
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Instructions') }}</h3>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li><strong>1.</strong> Choose a letter type</li>
                    <li><strong>2.</strong> Customize the header, body, and footer</li>
                    <li><strong>3.</strong> Use HTML tags for formatting</li>
                    <li><strong>4.</strong> Use variables like {{variable}}</li>
                    <li><strong>5.</strong> Preview before saving</li>
                </ul>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-medium text-yellow-800 mb-2"><i class="fas fa-info-circle mr-2"></i>Available Variables</h4>
                    <ul class="text-xs text-yellow-700 space-y-1">
                        <li><code>{{student_name}}</code></li>
                        <li><code>{{application_number}}</code></li>
                        <li><code>{{program}}</code></li>
                        <li><code>{{institution}}</code></li>
                        <li><code>{{letter_number}}</code></li>
                        <li><code>{{issue_date}}</code></li>
                        <li><code>{{response_deadline}}</code></li>
                        <li><code>{{interview_date}}</code></li>
                        <li><code>{{interview_venue}}</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="preview-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Template Preview</h3>
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="preview-content" class="p-6 prose max-w-none">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const defaults = @json(\App\Models\LetterTemplate::getDefaultContent('admission'));

function loadDefaults() {
    const type = document.getElementById('type').value;
    if (defaults[type]) {
        if (!document.getElementById('header_content').value) {
            document.getElementById('header_content').value = defaults[type].header || '';
        }
        if (!document.getElementById('body_content').value) {
            document.getElementById('body_content').value = defaults[type].body || '';
        }
        if (!document.getElementById('footer_content').value) {
            document.getElementById('footer_content').value = defaults[type].footer || '';
        }
    }
}

async function previewTemplate() {
    const header = document.getElementById('header_content').value;
    const body = document.getElementById('body_content').value;
    const footer = document.getElementById('footer_content').value;
    
    try {
        const response = await fetch("{{ route('admin.letter-templates.generate') }}?type=" + document.getElementById('type').value, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            method: 'POST',
            body: JSON.stringify({ header, body, footer })
        });
        const data = await response.json();
        
        document.getElementById('preview-content').innerHTML = 
            (data.header || '') + (data.body || '') + (data.footer || '');
        document.getElementById('preview-modal').classList.remove('hidden');
    } catch (e) {
        alert('Error loading preview');
    }
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}
</script>
@endpush
@endsection