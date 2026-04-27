@extends('layouts.admin')

@section('title', __('Bulk Generate Letters'))

@section('header', __('Bulk Generate Letters'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Bulk Generate Letters') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Generate letters for multiple approved applications at once</p>
        </div>
        <a href="{{ route('admin.admission-letters.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back') }}
        </a>
    </div>

    @if($applications->isEmpty())
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
            <p class="text-blue-700">No approved applications without existing letters found.</p>
        </div>
    </div>
    @else
    <form action="{{ route('admin.admission-letters.bulk-store') }}" method="POST" id="bulk-form">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Select Applications') }}</h3>
                        <div class="flex gap-2">
                            <button type="button" onclick="selectAll()" class="text-xs text-blue-600 hover:underline">Select All</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="deselectAll()" class="text-xs text-blue-600 hover:underline">Deselect All</button>
                        </div>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300">
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">App #</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Program</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($applications as $app)
                                <tr>
                                    <td class="px-4 py-2">
                                        <input type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="application-checkbox rounded border-gray-300">
                                    </td>
                                    <td class="px-4 py-2 text-sm">{{ $app->student?->fullName() ?? ($app->user?->first_name . ' ' . $app->user?->last_name) }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $app->application_number }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $app->program->name ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 border-t">
                        <span class="text-sm text-gray-600">Selected: <span id="selected-count">0</span> applications</span>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Letter Settings') }}</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Letter Type') }} *</label>
                                <select name="type" id="bulk-type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required onchange="toggleBulkInterviewFields()">
                                    <option value="admission" {{ $type === 'admission' ? 'selected' : '' }}>Admission Offer</option>
                                    <option value="calling" {{ $type === 'calling' ? 'selected' : '' }}>Calling/Interview Letter</option>
                                    <option value="provisional">Provisional Offer</option>
                                    <option value="rejection">Rejection Letter</option>
                                    <option value="deferral">Deferral Letter</option>
                                </select>
                            </div>
                            <div>
                                <label for="response_deadline" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Response Deadline') }}</label>
                                <input type="date" name="response_deadline" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]">
                            </div>
                        </div>

                        <div id="bulk-interview-fields" class="hidden">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="interview_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Interview Date & Time') }} *</label>
                                    <input type="datetime-local" name="interview_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]">
                                </div>
                                <div>
                                    <label for="interview_venue" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Interview Venue') }} *</label>
                                    <input type="text" name="interview_venue" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" placeholder="e.g., Main Campus">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="interview_instructions" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Instructions') }}</label>
                                <textarea name="interview_instructions" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="2"></textarea>
                            </div>
                        </div>

                        <div>
                            <label for="additional_conditions" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Additional Conditions') }}</label>
                            <textarea name="additional_conditions" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="2"></textarea>
                        </div>

                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Remarks') }}</label>
                            <textarea name="remarks" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Summary') }}</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Selected Applications:</span>
                            <span class="font-medium" id="summary-count">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Letter Type:</span>
                            <span class="font-medium" id="summary-type">Admission</span>
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-6 px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] font-medium">
                        <i class="fas fa-file-alt mr-2"></i> Generate Letters
                    </button>
                    <p class="text-xs text-gray-500 mt-3 text-center">This will generate and send letters to all selected applicants</p>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

@push('scripts')
<script>
const selectAllBtn = document.getElementById('select-all');
const checkboxes = document.querySelectorAll('.application-checkbox');
const selectedCount = document.getElementById('selected-count');
const summaryCount = document.getElementById('summary-count');
const summaryType = document.getElementById('summary-type');

selectAllBtn.addEventListener('change', function() {
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateCount();
});

checkboxes.forEach(cb => cb.addEventListener('change', updateCount));

function updateCount() {
    const count = document.querySelectorAll('.application-checkbox:checked').length;
    selectedCount.textContent = count;
    summaryCount.textContent = count;
}

function selectAll() {
    checkboxes.forEach(cb => cb.checked = true);
    updateCount();
}

function deselectAll() {
    checkboxes.forEach(cb => cb.checked = false);
    updateCount();
}

document.getElementById('bulk-type').addEventListener('change', function() {
    summaryType.textContent = this.options[this.selectedIndex].text;
});

function toggleBulkInterviewFields() {
    const type = document.getElementById('bulk-type').value;
    const fields = document.getElementById('bulk-interview-fields');
    if (type === 'calling') {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}
</script>
@endpush
@endsection