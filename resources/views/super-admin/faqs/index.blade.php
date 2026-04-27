@extends('layouts.super-admin')

@section('header', 'FAQ Management')
@section('breadcrumb', 'Manage all FAQs')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">FAQ Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage frequently asked questions across all schools</p>
        </div>
        <a href="{{ route('super-admin.faqs.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-purple-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add FAQ
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search_school" value="{{ request('search_school') }}" placeholder="Search school..."
                           class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="flex flex-wrap gap-2">
                    <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-500">
                        <option value="">All Categories</option>
                        <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="admission" {{ request('category') == 'admission' ? 'selected' : '' }}>Admission</option>
                        <option value="application" {{ request('category') == 'application' ? 'selected' : '' }}>Application</option>
                        <option value="payment" {{ request('category') == 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="support" {{ request('category') == 'support' ? 'selected' : '' }}>Support</option>
                    </select>
                    <label class="flex items-center text-sm">
                        <input type="checkbox" name="is_global" value="1" {{ request('is_global') ? 'checked' : '' }} class="mr-1">
                        Global
                    </label>
                    <button type="submit" class="px-3 sm:px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">School</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if($faq->school_id)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $faq->school?->name ?? 'N/A' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Global
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($faq->question, 50) }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($faq->answer, 60) }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize">
                                {{ $faq->category }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" onclick="toggleFaq({{ $faq->id }})" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $faq->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <a href="{{ route('super-admin.faqs.edit', $faq) }}" class="text-blue-600 hover:text-blue-800 p-1.5 inline-block">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('super-admin.faqs.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1.5">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                            No FAQs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($faqs as $faq)
                <div class="p-3 sm:p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap gap-1 mb-2">
                                @if($faq->school_id)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $faq->school?->name ?? 'N/A' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Global
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize">
                                    {{ $faq->category }}
                                </span>
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($faq->question, 60) }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($faq->answer, 80) }}</div>
                        </div>
                        <button type="button" onclick="toggleFaq({{ $faq->id }})" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $faq->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </div>
                    <div class="flex items-center justify-end gap-2 mt-2">
                        <a href="{{ route('super-admin.faqs.edit', $faq)" class="text-blue-600 hover:text-blue-800 p-1.5">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('super-admin.faqs.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-1.5">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    No FAQs found.
                </div>
            @endforelse
        </div>

        @if($faqs->hasPages())
        <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200 overflow-x-auto">
            {{ $faqs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleFaq(id) {
    fetch(`/super-admin/faqs/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
    });
}
</script>
@endpush
@endsection
