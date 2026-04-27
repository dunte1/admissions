@extends('layouts.admin')

@section('title', 'Edit FAQ')

@section('header', 'Edit FAQ')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.faqs.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to FAQs
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
            @csrf @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question <span class="text-red-500">*</span></label>
                    <input type="text" name="question" value="{{ old('question', $faq->question) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                    @error('question')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Answer <span class="text-red-500">*</span></label>
                    <textarea name="answer" rows="5" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">{{ old('answer', $faq->answer) }}</textarea>
                    @error('answer')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B]">
                            <option value="general" {{ $faq->category == 'general' ? 'selected' : '' }}>General</option>
                            <option value="admission" {{ $faq->category == 'admission' ? 'selected' : '' }}>Admission</option>
                            <option value="application" {{ $faq->category == 'application' ? 'selected' : '' }}>Application</option>
                            <option value="payment" {{ $faq->category == 'payment' ? 'selected' : '' }}>Payment</option>
                            <option value="support" {{ $faq->category == 'support' ? 'selected' : '' }}>Support</option>
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B]">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $faq->is_active ? 'checked' : '' }}
                           class="w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.faqs.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-[#00008B] text-white rounded-lg text-sm font-medium hover:bg-[#1e40af]">
                    Update FAQ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
