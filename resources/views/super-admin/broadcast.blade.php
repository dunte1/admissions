@extends('layouts.super-admin')

@section('page-title', 'Send Notification')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('super-admin.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                <h1 class="text-xl font-bold text-gray-900">Send System Notification</h1>
                <p class="text-sm text-gray-500">Broadcast a notification to all schools or selected schools</p>
            </div>

            <form action="{{ route('super-admin.broadcast') }}" method="POST" class="p-6">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Notification Title</label>
                        <input type="text" name="title" id="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter notification title">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea name="message" id="message" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your message"></textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="target" value="all" checked class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">All Schools</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="target" value="active" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Active Subscriptions Only</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="target" value="trial" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Trial Period Schools</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="target" value="expired" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Expired Subscriptions</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('super-admin.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-paper-plane mr-2"></i> Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
