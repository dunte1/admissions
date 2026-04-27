@extends('layouts.student')

@section('content')
<div class="max-w-7xl mx-auto" x-data="formFieldHandler()">
    {{-- Mobile Top Progress Bar --}}
    <div class="lg:hidden bg-white border-b border-gray-200 mb-4 px-3 py-3 -mx-3 sticky top-16 z-40">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">{{ $stepLabel }}</span>
            <span class="text-sm font-bold text-purple-600">{{ $completionPercentage }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ $completionPercentage }}%"></div>
        </div>
        <div class="flex items-center justify-between mt-2">
            <span class="text-xs text-gray-500">Step {{ array_search($step, array_keys($steps)) + 1 }} of {{ count($steps) }}</span>
            <a href="{{ route('student.dashboard') }}" class="text-xs text-gray-500 hover:text-gray-700">
                <i class="fas fa-times mr-1"></i>Exit
            </a>
        </div>
    </div>

    <div class="mb-4 sm:mb-8 hidden lg:block">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('labels.application_form') }}</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Step {{ array_search($step, array_keys($steps)) + 1 }} of {{ count($steps) }}: {{ $stepLabel }}</p>
            </div>
            <div class="text-left sm:text-right">
                <p class="text-sm font-medium text-gray-500">Application #</p>
                <p class="text-lg font-bold text-[#00008B]">{{ $application->application_number }}</p>
            </div>
        </div>
    </div>

    @if(session('saved'))
        <div class="mb-4 sm:mb-6 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="text-sm">{{ session('saved') }}</span>
        </div>
    @endif

    {{-- Mobile Step Indicator --}}
    <div class="lg:hidden mb-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-500">Step {{ array_search($step, array_keys($steps)) + 1 }} of {{ count($steps) }}</span>
                <span class="text-sm font-medium text-purple-600">{{ $stepLabel }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ $completionPercentage }}%"></div>
            </div>
            <div class="flex justify-between mt-3 overflow-x-auto gap-1">
                @php
                    $validSteps = ['personal', 'academic', 'guardian', 'documents', 'financial', 'declaration'];
                    $currentIndex = array_search($currentStep, $validSteps);
                @endphp
                @foreach($steps as $stepKey => $stepName)
                    @php
                        $stepIndex = array_search($stepKey, $validSteps);
                        $isCompleted = $completedSteps->contains($stepKey);
                        $isCurrent = $currentStep === $stepKey;
                        $canAccess = $stepIndex <= $currentIndex || $isCompleted || $stepIndex === 0;
                    @endphp
                    <a href="{{ $canAccess ? route('student.application.form', ['step' => $stepKey]) : '#' }}"
                       class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                       {{ $isCurrent ? 'bg-purple-600 text-white' : ($isCompleted ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600') }}
                       {{ !$canAccess ? 'opacity-50' : '' }}"
                       @if(!$canAccess) onclick="return false;" @endif>
                        @if($isCompleted && !$isCurrent)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            {{ $loop->index + 1 }}
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-8">
        {{-- Desktop Sidebar Only --}}
        <div class="hidden lg:block lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('labels.form_steps') }}</h3>
                <nav class="space-y-3">
                    @php
                        $validSteps = ['personal', 'academic', 'guardian', 'documents', 'financial', 'declaration'];
                        $currentIndex = array_search($currentStep, $validSteps);
                    @endphp
                    @foreach($steps as $stepKey => $stepLabel)
                        @php
                            $stepIndex = array_search($stepKey, $validSteps);
                            $isCompleted = $completedSteps->contains($stepKey);
                            $isCurrent = $currentStep === $stepKey;
                            $canAccess = $stepIndex <= $currentIndex || $isCompleted || $stepIndex === 0;
                        @endphp
                        <a href="{{ $canAccess ? route('student.application.form', ['step' => $stepKey]) : '#' }}"
                           class="flex items-center p-3 rounded-lg transition-all {{ $isCurrent ? 'bg-purple-100 text-purple-700' : ($isCompleted ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:bg-gray-100') }}
                           {{ !$canAccess ? 'pointer-events-none opacity-50' : '' }}"
                           @if(!$canAccess) onclick="return false;" @endif>
                            <span class="w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm font-semibold {{ $isCurrent ? 'bg-purple-600 text-white' : ($isCompleted ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600') }}">
                                @if($isCompleted && !$isCurrent)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    {{ $loop->index + 1 }}
                                @endif
                            </span>
                            <span class="font-medium">{{ $stepLabel }}</span>
                            @if(!$canAccess && !$isCompleted)
                                <i class="fas fa-lock ml-auto text-xs text-gray-400"></i>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ __('labels.application_status') }}</h4>
                    <div class="flex items-center">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-600">{{ $completionPercentage }}%</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('student.dashboard') }}" class="block w-full text-center bg-gray-100 text-gray-700 py-2.5 px-4 rounded-lg hover:bg-gray-200 transition-all text-sm">
                        {{ __('labels.save_exit') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-2">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">
                @if($step == 'personal')Personal Information
                @elseif($step == 'academic')Academic Background
                @elseif($step == 'guardian')Guardian Information
                @elseif($step == 'documents')Documents Upload
                @elseif($step == 'financial')Financial Information
                @elseif($step == 'declaration')Declaration
                @else{{ $stepLabel }}
                @endif
            </h2>
                    <span class="text-sm text-gray-500">{{ __('labels.required_fields') }}</span>
                </div>

                <form action="{{ route('student.application.save', ['step' => $step]) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="applicationForm" novalidate>
                    @csrf
                    
                    @if($step == 'declaration')
                        @include('student.forms.declaration')
                    @elseif($step == 'personal')
                        @include('student.forms.personal')
                    @elseif($step == 'academic')
                        @include('student.forms.academic')
                    @elseif($step == 'guardian')
                        @include('student.forms.guardian')
                    @elseif($step == 'documents')
                        @include('student.forms.documents')
                    @elseif($step == 'financial')
                        @include('student.forms.financial')
                    @elseif($step == 'declaration')
                        @include('student.forms.declaration')
                    @else
                        @forelse($formFields as $field)
                            {!! $field->render($sectionData[$field->key] ?? null) !!}
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <p>No form fields configured for this section.</p>
                            </div>
                        @endforelse
                    @endif

                    <div class="flex flex-col sm:flex-row justify-between pt-4 sm:pt-6 border-t border-gray-200 gap-3">
                        @if($step != 'personal')
                            <a href="{{ route('student.application.form', ['step' => $previousStep]) }}" class="order-2 sm:order-1 bg-gray-100 text-gray-700 py-3 sm:py-2.5 px-6 rounded-lg hover:bg-gray-200 transition-all font-medium text-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                {{ __('labels.previous') }}
                            </a>
                        @else
                            <div></div>
                        @endif

                        @if($step == 'declaration')
                            <button type="submit" class="order-1 sm:order-2 w-full sm:w-auto bg-green-600 text-white py-3 sm:py-2.5 px-8 rounded-lg hover:bg-green-700 transition-all font-semibold shadow-lg shadow-green-200">
                                <i class="fas fa-check-circle mr-2"></i>
                                Submit Application
                            </button>
                        @else
                            <button type="submit" class="order-1 sm:order-2 w-full sm:w-auto bg-[#00008B] text-white py-3 sm:py-2.5 px-8 rounded-lg hover:bg-[#1e40af] transition-all font-medium shadow-lg shadow-blue-200">
                                {{ __('labels.next') }}
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function formFieldHandler() {
    return {
        fieldValues: {},
        
        init() {
            // Initialize with data from formData for current step
            @php
            $currentStepData = $formData[$step] ?? [];
            @endphp
            @foreach($currentStepData as $key => $value)
                this.fieldValues['{{ $key }}'] = '{{ is_array($value) ? json_encode($value) : $value }}';
            @endforeach
        },
        
        getFieldValue(key) {
            const el = document.querySelector(`[name="${key}"]`);
            if (!el) return null;
            
            if (el.type === 'checkbox') {
                return el.checked ? el.value : '';
            }
            if (el.type === 'radio') {
                const checked = document.querySelector(`[name="${key}"]:checked`);
                return checked ? checked.value : '';
            }
            return el.value;
        }
    }
}
</script>
@endpush
