@extends('layouts.admin')

@section('title', 'FAQ Management')

@section('header', 'FAQ Management')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">FAQ Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage frequently asked questions for students</p>
        </div>
        <a href="{{ route('admin.faqs.create') }}" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
            <i class="fas fa-plus mr-2"></i> Add FAQ
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                </div>
                <div>
                    <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B]">
                        <option value="">All Categories</option>
                        <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="admission" {{ request('category') == 'admission' ? 'selected' : '' }}>Admission</option>
                        <option value="application" {{ request('category') == 'application' ? 'selected' : '' }}>Application</option>
                        <option value="payment" {{ request('category') == 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="support" {{ request('category') == 'support' ? 'selected' : '' }}>Support</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-[#00008B] text-white rounded-lg text-sm hover:bg-[#1e40af]">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('admin.faqs.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                    Clear
                </a>
                @endif
            </form>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $faq->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($faq->question, 60) }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($faq->answer, 80) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 capitalize">
                                {{ $faq->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button type="button" onclick="toggleFaq({{ $faq->id }})" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $faq->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No FAQs found. <a href="{{ route('admin.faqs.create') }}" class="text-blue-600 hover:underline">Create one</a>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($faqs as $faq)
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-xs text-gray-500">Order: {{ $faq->sort_order }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $faq->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-sm font-medium text-gray-900 mb-1">{{ $faq->question }}</p>
                <p class="text-xs text-gray-500 mb-3">{{ Str::limit($faq->answer, 100) }}</p>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 capitalize">{{ $faq->category }}</span>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-sm text-blue-600 hover:text-blue-800"><i class="fas fa-edit mr-1"></i>Edit</a>
                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800"><i class="fas fa-trash mr-1"></i>Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                No FAQs found. <a href="{{ route('admin.faqs.create') }}" class="text-blue-600 hover:underline">Create one</a>
            </div>
            @endforelse
        </div>

        @if($faqs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $faqs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleFaq(id) {
    fetch(`/admin/faqs/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
@endsection
