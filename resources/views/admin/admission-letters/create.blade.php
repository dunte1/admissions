@extends('layouts.admin')

@section('title', __('Generate Admission Letter'))

@section('header', __('Generate Admission Letter'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Generate Admission Letter') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Create admission letters for approved applications</p>
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Select Application') }}</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.admission-letters.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="application_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Application') }} *</label>
                            <select name="application_id" id="application_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required>
                                <option value="">-- Select Application --</option>
                                @foreach($applications as $app)
                                    <option value="{{ $app->id }}" {{ old('application_id') == $app->id ? 'selected' : '' }}>
                                        {{ $app->student?->fullName() ?? ($app->user?->first_name . ' ' . $app->user?->last_name ?? 'N/A') }} - {{ $app->application_number }} - {{ $app->program->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Letter Type') }} *</label>
                            <select name="type" id="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required onchange="toggleInterviewFields()">
                                <option value="">-- Select Type --</option>
                                <option value="admission" {{ old('type') == 'admission' ? 'selected' : '' }}>Admission Offer</option>
                                <option value="calling" {{ old('type') == 'calling' ? 'selected' : '' }}>Calling/Interview Letter</option>
                                <option value="provisional" {{ old('type') == 'provisional' ? 'selected' : '' }}>Provisional Offer</option>
                                <option value="rejection" {{ old('type') == 'rejection' ? 'selected' : '' }}>Rejection Letter</option>
                                <option value="deferral" {{ old('type') == 'deferral' ? 'selected' : '' }}>Deferral Letter</option>
                            </select>
                        </div>

                        <div id="interview-fields" class="hidden">
                            <div class="mb-4">
                                <label for="interview_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Interview Date & Time') }} *</label>
                                <input type="datetime-local" name="interview_date" id="interview_date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" 
                                       value="{{ old('interview_date') }}">
                            </div>

                            <div class="mb-4">
                                <label for="interview_venue" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Interview Venue') }} *</label>
                                <input type="text" name="interview_venue" id="interview_venue" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" 
                                       value="{{ old('interview_venue') }}" placeholder="e.g., Main Campus, Room 101">
                            </div>

                            <div class="mb-4">
                                <label for="interview_instructions" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Instructions') }}</label>
                                <textarea name="interview_instructions" id="interview_instructions" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="3"
                                          placeholder="What to bring, dress code, etc.">{{ old('interview_instructions') }}</textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="response_deadline" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Response Deadline') }}</label>
                            <input type="date" name="response_deadline" id="response_deadline" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" 
                                   value="{{ old('response_deadline', now()->addMonth()->format('Y-m-d')) }}"
                                   min="{{ now()->addDay()->format('Y-m-d') }}">
                            <p class="mt-1 text-sm text-gray-500">Leave empty for no deadline</p>
                        </div>

                        <div class="mb-4">
                            <label for="additional_conditions" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Additional Conditions') }}</label>
                            <textarea name="additional_conditions" id="additional_conditions" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="3"
                                      placeholder="List any conditions the student must meet...">{{ old('additional_conditions') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Remarks') }}</label>
                            <textarea name="remarks" id="remarks" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" rows="2"
                                      placeholder="Any additional remarks...">{{ old('remarks') }}</textarea>
                        </div>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                            <i class="fas fa-file-alt mr-2"></i> Generate Letter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Instructions') }}</h3>
                </div>
                <div class="p-6">
                    <ol class="list-decimal list-inside space-y-3 text-sm text-gray-600">
                        <li>Select the approved application</li>
                        <li>Choose the letter type (Admission, Calling, etc.)</li>
                        <li>For Calling Letter, enter interview details</li>
                        <li>Set a response deadline (optional)</li>
                        <li>Add any conditions or remarks</li>
                        <li>Click Generate to create the letter</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function toggleInterviewFields() {
    const type = document.getElementById('type').value;
    const fields = document.getElementById('interview-fields');
    const interviewDate = document.getElementById('interview_date');
    const interviewVenue = document.getElementById('interview_venue');
    
    if (type === 'calling') {
        fields.classList.remove('hidden');
        interviewDate.required = true;
        interviewVenue.required = true;
    } else {
        fields.classList.add('hidden');
        interviewDate.required = false;
        interviewVenue.required = false;
    }
}
</script>
@endsection
