@extends('layouts.student')

@section('title', 'Select Program')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ __('labels.new_application') }}</h1>
        <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base">Select your preferred programs and get started</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-8">
        <form action="{{ route('student.application.store') }}" method="POST" id="programForm">
            @csrf
            
            <div class="mb-6 sm:mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                    <span class="w-8 h-8 bg-[#00008B] text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                    Choose Your Programs
                </h2>
                <p class="text-sm text-gray-500 ml-11 mb-4 sm:mb-6">Select your first, second, and third choice programs. Your first choice will be given priority during review.</p>
            </div>

            {{-- Program Choices --}}
            <div class="space-y-4 sm:space-y-6 mb-6 sm:mb-8">
                {{-- First Choice --}}
                <div class="p-4 sm:p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
                    <div class="flex items-center mb-3 sm:mb-4">
                        <span class="flex items-center justify-center w-8 h-8 bg-[#00008B] text-white rounded-full text-sm font-bold mr-3">1st</span>
                        <h3 class="font-semibold text-gray-900">First Choice (Priority)</h3>
                        <span class="ml-2 text-xs bg-[#00008B] text-white px-2 py-0.5 rounded-full">Required</span>
                    </div>
                    <select name="program_id_1" id="program1" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B] text-sm sm:text-base" 
                        onchange="updateProgramDetails(1)">
                        <option value="">-- Select First Choice Program --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" 
                                data-level="{{ $program->level }}"
                                data-duration="{{ $program->duration_years }}"
                                data-requirements="{{ $program->requirements ?? 'None specified' }}"
                                data-tution="{{ $program->tuition_per_year }}"
                                data-capacity="{{ $program->capacity }}"
                                data-available="{{ $program->available_slots }}"
                                data-full="{{ $program->is_full ? '1' : '0' }}"
                                {{ $program->is_full ? 'disabled' : '' }}>
                                {{ $program->name }} ({{ $program->code }})
                                @if($program->capacity)
                                    @if($program->is_full)
                                        - FULL (No slots)
                                    @else
                                        - {{ $program->available_slots }} slots left
                                    @endif
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div id="program1Details" class="mt-4 hidden">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 text-sm">
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Level</p>
                                <p class="font-semibold text-gray-900" id="program1Level">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Duration</p>
                                <p class="font-semibold text-gray-900" id="program1Duration">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Tuition/Year</p>
                                <p class="font-semibold text-gray-900" id="program1Tuition">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Requirements</p>
                                <p class="font-semibold text-gray-900" id="program1Requirements">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg col-span-2">
                                <p class="text-gray-500">Available Slots</p>
                                <p class="font-semibold text-gray-900" id="program1Slots">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Second Choice --}}
                <div class="p-4 sm:p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-2 border-green-200">
                    <div class="flex items-center mb-3 sm:mb-4">
                        <span class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full text-sm font-bold mr-3">2nd</span>
                        <h3 class="font-semibold text-gray-900">Second Choice</h3>
                        <span class="ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Optional</span>
                    </div>
                    <select name="program_id_2" id="program2" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm sm:text-base"
                        onchange="updateProgramDetails(2)">
                        <option value="">-- Select Second Choice (Optional) --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" 
                                data-level="{{ $program->level }}"
                                data-duration="{{ $program->duration_years }}"
                                data-requirements="{{ $program->requirements ?? 'None specified' }}"
                                data-tution="{{ $program->tuition_per_year }}"
                                data-capacity="{{ $program->capacity }}"
                                data-available="{{ $program->available_slots }}"
                                data-full="{{ $program->is_full ? '1' : '0' }}"
                                {{ $program->is_full ? 'disabled' : '' }}>
                                {{ $program->name }} ({{ $program->code }})
                                @if($program->capacity)
                                    @if($program->is_full)
                                        - FULL (No slots)
                                    @else
                                        - {{ $program->available_slots }} slots left
                                    @endif
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div id="program2Details" class="mt-4 hidden">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 text-sm">
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Level</p>
                                <p class="font-semibold text-gray-900" id="program2Level">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Duration</p>
                                <p class="font-semibold text-gray-900" id="program2Duration">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Tuition/Year</p>
                                <p class="font-semibold text-gray-900" id="program2Tuition">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Requirements</p>
                                <p class="font-semibold text-gray-900" id="program2Requirements">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg col-span-2">
                                <p class="text-gray-500">Available Slots</p>
                                <p class="font-semibold text-gray-900" id="program2Slots">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Third Choice --}}
                <div class="p-4 sm:p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200">
                    <div class="flex items-center mb-3 sm:mb-4">
                        <span class="flex items-center justify-center w-8 h-8 bg-purple-600 text-white rounded-full text-sm font-bold mr-3">3rd</span>
                        <h3 class="font-semibold text-gray-900">Third Choice</h3>
                        <span class="ml-2 text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Optional</span>
                    </div>
                    <select name="program_id_3" id="program3" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm sm:text-base"
                        onchange="updateProgramDetails(3)">
                        <option value="">-- Select Third Choice (Optional) --</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" 
                                data-level="{{ $program->level }}"
                                data-duration="{{ $program->duration_years }}"
                                data-requirements="{{ $program->requirements ?? 'None specified' }}"
                                data-tution="{{ $program->tuition_per_year }}"
                                data-capacity="{{ $program->capacity }}"
                                data-available="{{ $program->available_slots }}"
                                data-full="{{ $program->is_full ? '1' : '0' }}"
                                {{ $program->is_full ? 'disabled' : '' }}>
                                {{ $program->name }} ({{ $program->code }})
                                @if($program->capacity)
                                    @if($program->is_full)
                                        - FULL (No slots)
                                    @else
                                        - {{ $program->available_slots }} slots left
                                    @endif
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div id="program3Details" class="mt-4 hidden">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 text-sm">
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Level</p>
                                <p class="font-semibold text-gray-900" id="program3Level">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Duration</p>
                                <p class="font-semibold text-gray-900" id="program3Duration">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Tuition/Year</p>
                                <p class="font-semibold text-gray-900" id="program3Tuition">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg">
                                <p class="text-gray-500">Requirements</p>
                                <p class="font-semibold text-gray-900" id="program3Requirements">-</p>
                            </div>
                            <div class="bg-white p-3 rounded-lg col-span-2">
                                <p class="text-gray-500">Available Slots</p>
                                <p class="font-semibold text-gray-900" id="program3Slots">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Info --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 sm:p-6 mb-6 sm:mb-8">
                <h3 class="font-semibold text-amber-800 mb-2 sm:mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Before You Continue
                </h3>
                <ul class="text-sm text-amber-700 space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        Your first choice will be processed first. If not successful, we'll consider your second and third choices.
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        Application fee is KES {{ number_format($applicationFee) }} (non-refundable)
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
                        You can save your application as draft and continue later.
                    </li>
                </ul>
            </div>

            {{-- Eligibility Checker --}}
            <div class="mb-6 sm:mb-8">
                <button type="button" onclick="toggleEligibilityChecker()" class="flex items-center text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-search mr-2"></i>
                    Check Program Eligibility with Your Grades
                </button>
                
                <div id="eligibilityChecker" class="hidden mt-4 p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Required:</strong> You must check your eligibility before proceeding. This helps ensure you meet the minimum requirements for your chosen program level.
                        </p>
                    </div>
                    
                    <h4 class="font-semibold text-gray-900 mb-4">Enter Your KCSE Grades</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your KCSE Mean Grade *</label>
                            <select id="checkGrade" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm" required>
                                <option value="">Select your mean grade</option>
                                <option value="A">A - Plain</option>
                                <option value="A-">A- (Minus)</option>
                                <option value="B+">B+ (Plus)</option>
                                <option value="B">B - Plain</option>
                                <option value="B-">B- (Minus)</option>
                                <option value="C+">C+ (Plus)</option>
                                <option value="C">C - Plain</option>
                                <option value="C-">C- (Minus)</option>
                                <option value="D+">D+ (Plus)</option>
                                <option value="D">D - Plain</option>
                                <option value="D-">D- (Minus)</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Intended Level *</label>
                            <select id="checkLevel" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm" required>
                                <option value="">Select level</option>
                                <option value="certificate">Certificate</option>
                                <option value="diploma">Diploma</option>
                                <option value="degree">Degree</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Key Subject Grades (select if you have them)</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @php
                                $subjects = [
                                    'biology' => 'Biology',
                                    'chemistry' => 'Chemistry',
                                    'physics' => 'Physics',
                                    'mathematics' => 'Mathematics',
                                    'english' => 'English',
                                    'kiswahili' => 'Kiswahili',
                                ];
                                $grades = ['' => '-', 'A' => 'A', 'A-' => 'A-', 'B+' => 'B+', 'B' => 'B', 'B-' => 'B-', 'C+' => 'C+', 'C' => 'C', 'C-' => 'C-', 'D+' => 'D+', 'D' => 'D', 'D-' => 'D-', 'E' => 'E'];
                            @endphp
                            @foreach($subjects as $key => $label)
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">{{ $label }}</label>
                                <select id="subject_{{ $key }}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm">
                                    @foreach($grades as $g => $label)
                                        <option value="{{ $g }}">{{ $label ?: '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="button" onclick="checkEligibility()" class="bg-[#00008B] text-white px-4 py-2.5 rounded-lg hover:bg-[#1e40af] text-sm font-medium w-full sm:w-auto">
                        Check Eligibility
                    </button>
                    
                    <div id="eligibilityResults" class="mt-4 hidden">
                        <div id="eligibilityResultBox"></div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 sm:pt-6 border-t border-gray-200">
                <a href="{{ route('student.dashboard') }}" class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 text-center font-medium">
                    {{ __('labels.cancel') }}
                </a>
                <button type="submit" class="w-full sm:w-auto bg-[#00008B] text-white px-8 py-3 rounded-lg hover:bg-[#1e40af] shadow-lg shadow-blue-200 font-semibold flex items-center justify-center">
                    <i class="fas fa-arrow-right mr-2"></i>
                    Start Application
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateProgramDetails(num) {
    const select = document.getElementById('program' + num);
    const detailsDiv = document.getElementById('program' + num + 'Details');
    const selectedOption = select.options[select.selectedIndex];
    
    if (select.value) {
        detailsDiv.classList.remove('hidden');
        document.getElementById('program' + num + 'Level').textContent = selectedOption.dataset.level ? ucfirst(selectedOption.dataset.level) : '-';
        document.getElementById('program' + num + 'Duration').textContent = selectedOption.dataset.duration ? selectedOption.dataset.duration + ' Years' : '-';
        document.getElementById('program' + num + 'Tuition').textContent = selectedOption.dataset.tution ? 'KES ' + numberFormat(selectedOption.dataset.tution) : '-';
        document.getElementById('program' + num + 'Requirements').textContent = selectedOption.dataset.requirements ? selectedOption.dataset.requirements.substring(0, 50) + '...' : '-';
        
        const capacity = selectedOption.dataset.capacity;
        const available = selectedOption.dataset.available;
        const isFull = selectedOption.dataset.full === '1';
        
        if (capacity && capacity > 0) {
            if (isFull) {
                document.getElementById('program' + num + 'Slots').textContent = 'FULL - No slots available';
                document.getElementById('program' + num + 'Slots').classList.add('text-red-600');
            } else {
                document.getElementById('program' + num + 'Slots').textContent = available + ' slots remaining';
                document.getElementById('program' + num + 'Slots').classList.remove('text-red-600');
                document.getElementById('program' + num + 'Slots').classList.add('text-green-600');
            }
        } else {
            document.getElementById('program' + num + 'Slots').textContent = 'Unlimited';
            document.getElementById('program' + num + 'Slots').classList.remove('text-red-600');
        }
    } else {
        detailsDiv.classList.add('hidden');
    }
}

function toggleEligibilityChecker() {
    const checker = document.getElementById('eligibilityChecker');
    checker.classList.toggle('hidden');
}

let eligibilityChecked = false;
let eligibilityPassed = false;

async function checkEligibility() {
    const grade = document.getElementById('checkGrade').value;
    const level = document.getElementById('checkLevel').value;
    const resultsDiv = document.getElementById('eligibilityResults');
    const resultBox = document.getElementById('eligibilityResultBox');
    
    if (!grade || !level) {
        alert('Please select both your grade and intended level');
        return;
    }

    const subjects = {};
    const subjectKeys = ['biology', 'chemistry', 'physics', 'mathematics', 'english', 'kiswahili'];
    subjectKeys.forEach(key => {
        const val = document.getElementById('subject_' + key)?.value;
        if (val) subjects[key] = val;
    });

    resultBox.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-[#00008B]"></i><p class="text-gray-600 mt-2">Checking eligibility...</p></div>';
    resultsDiv.classList.remove('hidden');

    const gradePoints = {
        'A': 12, 'A-': 11, 'B+': 10, 'B': 9, 'B-': 8, 
        'C+': 7, 'C': 6, 'C-': 5, 'D+': 4, 'D': 3, 'E': 1
    };
    
    const requirements = {
        'certificate': 4,
        'diploma': 7,
        'degree': 9
    };
    
    const points = gradePoints[grade] || 0;
    const required = requirements[level] || 0;
    eligibilityPassed = points >= required;
    eligibilityChecked = true;

    if (eligibilityPassed) {
        resultBox.className = 'p-4 rounded-lg bg-green-100 border border-green-200';
        resultBox.innerHTML = `
            <div class="flex items-center text-green-800 mb-3">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold">You're eligible for ${ucfirst(level)} programs!</p>
                    <p class="text-sm">Your grade (${grade}) meets the minimum requirement.</p>
                </div>
            </div>
            <p class="text-sm text-green-700"><i class="fas fa-check mr-1"></i> You can now proceed to start your application.</p>
        `;
    } else {
        resultBox.className = 'p-4 rounded-lg bg-yellow-100 border border-yellow-200';
        resultBox.innerHTML = `
            <div class="flex items-center text-yellow-800 mb-3">
                <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold">You may not meet the requirements for ${ucfirst(level)} programs.</p>
                    <p class="text-sm">Your grade (${grade}) is below the typical requirement.</p>
                </div>
            </div>
            <p class="text-sm text-yellow-700"><i class="fas fa-info-circle mr-1"></i> Consider checking lower-level programs or different program choices.</p>
        `;
    }
}

function ucfirst(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
}

function numberFormat(num) {
    return new Intl.NumberFormat('en-KE').format(num);
}

function validateForm() {
    const program1 = document.getElementById('program1').value;
    
    if (!program1) {
        alert('Please select your first choice program');
        return false;
    }
    
    const selectedOption = document.getElementById('program1').options[document.getElementById('program1').selectedIndex];
    if (selectedOption.dataset.full === '1') {
        alert('This program is full. Please select another program with available slots.');
        return false;
    }
    
    if (!eligibilityChecked) {
        alert('Please check your program eligibility first by entering your grades and clicking "Check Eligibility"');
        document.getElementById('eligibilityChecker').classList.remove('hidden');
        return false;
    }
    
    return true;
}

document.getElementById('programForm').addEventListener('submit', function(e) {
    if (!validateForm()) {
        e.preventDefault();
    }
});
</script>
@endsection
