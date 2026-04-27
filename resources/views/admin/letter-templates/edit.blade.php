@extends('layouts.admin')

@section('title', __('Edit Letter Template'))

@section('header', __('Edit Letter Template'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.letter-templates.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Edit Letter Template') }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <form action="{{ route('admin.letter-templates.update', $letterTemplate->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Template Name') }} *</label>
                                <input type="text" name="name" id="name" value="{{ $letterTemplate->name }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Letter Type') }} *</label>
                                <input type="text" value="{{ ucfirst($letterTemplate->type) }}" class="w-full bg-gray-100 rounded-lg border-gray-300" readonly>
                                <input type="hidden" name="type" value="{{ $letterTemplate->type }}">
                            </div>
                        </div>

                        <div>
                            <label for="header_content" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Header Content (HTML)') }}</label>
                            <textarea name="header_content" id="header_content" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="4">{{ $letterTemplate->header_content }}</textarea>
                        </div>

                        <div>
                            <label for="body_content" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Body Content (HTML)') }}</label>
                            <textarea name="body_content" id="body_content" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="8">{{ $letterTemplate->body_content }}</textarea>
                        </div>

                        <div>
                            <label for="footer_content" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Footer Content (HTML)') }}</label>
                            <textarea name="footer_content" id="footer_content" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="3">{{ $letterTemplate->footer_content }}</textarea>
                        </div>

                        <div>
                            <label for="change_note" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Change Note') }}</label>
                            <input type="text" name="change_note" id="change_note" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" placeholder="Describe changes made...">
                            <p class="mt-1 text-xs text-gray-500">This note will be saved in version history</p>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="previewTemplate()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-eye mr-2"></i> Preview
                            </button>
                            <button type="submit" class="px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af]">
                                <i class="fas fa-save mr-2"></i> Update Template
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Version History') }}</h3>
                <div class="space-y-3">
                    @forelse($letterTemplate->versions->take(5) as $version)
                    <div class="p-3 border rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">v{{ $version->version }}</span>
                            <span class="text-xs text-gray-500">{{ $version->created_at->format('M d, H:i') }}</span>
                        </div>
                        @if($version->change_note)
                        <p class="text-xs text-gray-600 mt-1">{{ $version->change_note }}</p>
                        @endif
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No versions yet</p>
                    @endforelse
                </div>
                @if($letterTemplate->versions->count() > 5)
                <a href="{{ route('admin.letter-templates.history', $letterTemplate->id) }}" class="block mt-4 text-sm text-blue-600 hover:underline">
                    View all versions →
                </a>
                @endif
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
async function previewTemplate() {
    const header = document.getElementById('header_content').value;
    const body = document.getElementById('body_content').value;
    const footer = document.getElementById('footer_content').value;
    
    try {
        const response = await fetch("{{ route('admin.letter-templates.generate') }}", {
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