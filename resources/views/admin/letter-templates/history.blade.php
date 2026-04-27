@extends('layouts.admin')

@section('title', __('Template History'))

@section('header', __('Version History'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.letter-templates.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Template History') }}</h1>
            <p class="text-sm text-gray-500">{{ $letterTemplate->name }} ({{ ucfirst($letterTemplate->type) }})</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Change Note</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($versions as $version)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            v{{ $version->version }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $version->change_note ?? 'No note' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ $version->creator?->name ?? 'System' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-500">{{ $version->created_at->format('M d, Y H:i') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex justify-end gap-2">
                            <button onclick="viewVersion({{ $version->id }})" class="text-blue-600 hover:text-blue-900 text-sm">
                                <i class="fas fa-eye"></i> View
                            </button>
                            @if($version->version !== $letterTemplate->versions()->max('version'))
                            <form action="{{ route('admin.letter-templates.revert', [$letterTemplate->id, $version->version]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-indigo-600 hover:text-indigo-900 text-sm" onclick="return confirm('Revert to this version?')">
                                    <i class="fas fa-undo"></i> Revert
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        No version history found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $versions->links() }}
    </div>
</div>

<div id="version-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[80vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Version <span id="modal-version"></span> Content</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Header</h4>
                    <div class="p-4 bg-gray-50 rounded-lg border text-sm font-mono whitespace-pre-wrap" id="modal-header"></div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Body</h4>
                    <div class="p-4 bg-gray-50 rounded-lg border text-sm font-mono whitespace-pre-wrap" id="modal-body"></div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Footer</h4>
                    <div class="p-4 bg-gray-50 rounded-lg border text-sm font-mono whitespace-pre-wrap" id="modal-footer"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
const versions = @json($versions->items());

function viewVersion(id) {
    const version = versions.find(v => v.id === id);
    if (version) {
        document.getElementById('modal-version').textContent = version.version;
        document.getElementById('modal-header').textContent = version.header_content || '(empty)';
        document.getElementById('modal-body').textContent = version.body_content || '(empty)';
        document.getElementById('modal-footer').textContent = version.footer_content || '(empty)';
        document.getElementById('version-modal').classList.remove('hidden');
    }
}

function closeModal() {
    document.getElementById('version-modal').classList.add('hidden');
}
</script>
@endpush
@endsection