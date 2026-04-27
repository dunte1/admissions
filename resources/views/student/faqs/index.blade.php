@extends('layouts.student')

@section('title', 'Frequently Asked Questions')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h1 class="text-2xl font-bold text-gray-900">Frequently Asked Questions</h1>
            <p class="text-sm text-gray-500 mt-1">Find answers to common questions about the admission process</p>
        </div>

        @if($categories->count() > 0)
        <div class="p-4 border-b border-gray-200 bg-white">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('student.faqs.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('category') ? 'bg-[#00008B] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All
                </a>
                @foreach($categories as $category)
                <a href="{{ route('student.faqs.index', ['category' => $category]) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors capitalize {{ request('category') === $category ? 'bg-[#00008B] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $category }}
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="p-6">
            @forelse($faqs as $faq)
            <div class="border border-gray-200 rounded-lg overflow-hidden mb-4 last:mb-0">
                <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left" onclick="toggleFaq(this)">
                    <span class="font-medium text-gray-900">{{ $faq->question }}</span>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                </button>
                <div class="hidden p-4 bg-white">
                    <p class="text-gray-600">{{ $faq->answer }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <i class="fas fa-question-circle text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No FAQs available at the moment.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleFaq(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i.fa-chevron-down');
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script>
@endpush
