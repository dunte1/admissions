@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('labels.send_notification') }}</h1>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('labels.recipient_type') }}</label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="recipient_type" value="all" class="mr-3" required>
                        <span>All Applicants</span>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="recipient_type" value="status" class="mr-3">
                        <span>By Status</span>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="recipient_type" value="individual" class="mr-3">
                        <span>Select Individuals</span>
                    </label>
                </div>
            </div>

            <div id="status-select" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.status') }}</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Select Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="under_review">Under Review</option>
                </select>
            </div>

            <div id="individual-select" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.select_applications') }}</label>
                <select name="application_ids[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg h-32">
                    @foreach(\App\Models\Application::with('student')->get() as $app)
                        <option value="{{ $app->id }}">{{ $app->application_number }} - {{ $app->student->full_name ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.notification_type') }}</label>
                <div class="grid grid-cols-3 gap-4">
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="notification_type" value="email" class="mr-3" checked>
                        <span>Email Only</span>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="notification_type" value="sms" class="mr-3">
                        <span>SMS Only</span>
                    </label>
                    <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="notification_type" value="both" class="mr-3">
                        <span>Both</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.subject') }}</label>
                <input type="text" name="subject" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('labels.message') }}</label>
                <textarea name="message" rows="6" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700">
                {{ __('labels.send_notification') }}
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('status-select').classList.add('hidden');
        document.getElementById('individual-select').classList.add('hidden');
        
        if (this.value === 'status') {
            document.getElementById('status-select').classList.remove('hidden');
        } else if (this.value === 'individual') {
            document.getElementById('individual-select').classList.remove('hidden');
        }
    });
});
</script>
@endpush
