<div x-data="{
    educationLevel: '{{ $formData['academic']['education_level'] ?? '' }}',
    showKcseFields: false,
    showGradeFields: false,
    showSubjects: false,
    init() {
        this.updateVisibility();
    },
    updateVisibility() {
        this.showKcseFields = ['kcse', 'o-level'].includes(this.educationLevel);
        this.showGradeFields = ['certificate', 'diploma', 'degree', 'masters'].includes(this.educationLevel);
        this.showSubjects = ['kcse', 'o-level'].includes(this.educationLevel);
    },
    calculateMeanGrade() {
        const subjects = ['english', 'kiswahili', 'mathematics', 'biology', 'physics', 'chemistry', 'computer_studies', 'home_science', 'geography', 'agriculture', 'commerce', 'business_studies'];
        const gradePoints = {
            'A': 12, 'A-': 11, 'B+': 10, 'B': 9, 'B-': 8, 
            'C+': 7, 'C': 6, 'C-': 5, 'D+': 4, 'D': 3, 'D-': 2, 'E': 1
        };
        
        let totalPoints = 0;
        let count = 0;
        
        subjects.forEach(subject => {
            const select = document.querySelector(`[name='subjects[${subject}]']`);
            const grade = select?.value;
            if (grade && gradePoints[grade] !== undefined) {
                totalPoints += gradePoints[grade];
                count++;
            }
        });
        
        if (count > 0) {
            const avgPoints = totalPoints / count;
            let meanGrade = '';
            
            if (avgPoints >= 11.5) meanGrade = 'A';
            else if (avgPoints >= 10.5) meanGrade = 'A-';
            else if (avgPoints >= 9.5) meanGrade = 'B+';
            else if (avgPoints >= 8.5) meanGrade = 'B';
            else if (avgPoints >= 7.5) meanGrade = 'B-';
            else if (avgPoints >= 6.5) meanGrade = 'C+';
            else if (avgPoints >= 5.5) meanGrade = 'C';
            else if (avgPoints >= 4.5) meanGrade = 'C-';
            else if (avgPoints >= 3.5) meanGrade = 'D+';
            else if (avgPoints >= 2.5) meanGrade = 'D';
            else meanGrade = 'E';
            
            document.querySelector('[name=\"mean_grade\"]').value = meanGrade;
            document.getElementById('calculatedMeanGrade').textContent = meanGrade;
        }
    }
}" class="space-y-6">

    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Programme Applied For *</label>
        <select name="programme_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            <option value="">-- Select Programme --</option>
            <optgroup label="DIPLOMA PROGRAMMES">
                <option value="dip-nursing">Diploma in Nursing (KRCHN)</option>
                <option value="dip-perioperative">Diploma in Perioperative Theatre Technology (Level 6)</option>
                <option value="dip-health-records">Diploma in Health Records & Information Technology</option>
                <option value="dip-community-hiv">Diploma in Community Health & HIV & AIDS</option>
                <option value="dip-health-support">Diploma in Health Services Support (Level 6)</option>
            </optgroup>
            <optgroup label="CERTIFICATE PROGRAMMES">
                <option value="cert-perioperative">Certificate in Perioperative Theatre Technology (Level 5)</option>
                <option value="cert-health-records">Certificate in Health Records & IT</option>
                <option value="cert-community-hiv">Certificate in Community Health & HIV & AIDS</option>
                <option value="artisan-health">Artisan in Health Services Support (Level 4)</option>
                <option value="cert-health-support">Certificate in Health Services Support (Level 5) / CNA / Caregiver</option>
            </optgroup>
            <optgroup label="SHORT COURSES">
                <option value="short-bls">BLS / ACLS (1 Week)</option>
                <option value="short-computer">Computer Packages</option>
                <option value="short-nclex">NCLEX Virtual Training</option>
            </optgroup>
        </select>
    </div>

    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Preferred Intake *</label>
        <select name="intake" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            <option value="">Select Intake</option>
            <option value="march-2026">March 2026</option>
            <option value="september-2026">September 2026</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Education Level *</label>
        <select name="education_level" x-model="educationLevel" @change="updateVisibility()" required 
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            <option value="">Select Level</option>
            <option value="kcse" {{ ($formData['academic']['education_level'] ?? '') == 'kcse' ? 'selected' : '' }}>O Level (KCSE)</option>
            <option value="kace" {{ ($formData['academic']['education_level'] ?? '') == 'kace' ? 'selected' : '' }}>A Level (KACE)</option>
            <option value="certificate" {{ ($formData['academic']['education_level'] ?? '') == 'certificate' ? 'selected' : '' }}>Certificate</option>
            <option value="diploma" {{ ($formData['academic']['education_level'] ?? '') == 'diploma' ? 'selected' : '' }}>Diploma</option>
            <option value="degree" {{ ($formData['academic']['education_level'] ?? '') == 'degree' ? 'selected' : '' }}>Degree</option>
            <option value="masters" {{ ($formData['academic']['education_level'] ?? '') == 'masters' ? 'selected' : '' }}>Masters</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">School Name *</label>
            <input type="text" name="institution_name" value="{{ $formData['academic']['institution_name'] ?? '' }}" required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Name of school/college/university">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Year From *</label>
            <select name="year_from" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">Select Year</option>
                @for($year = 2025; $year >= 2000; $year--)
                    <option value="{{ $year }}" {{ ($formData['academic']['year_from'] ?? '') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Year To *</label>
            <select name="year_to" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">Select Year</option>
                @for($year = 2025; $year >= 2000; $year--)
                    <option value="{{ $year }}" {{ ($formData['academic']['year_to'] ?? '') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>
        </div>
        <div x-show="showKcseFields" x-transition>
            <label class="block text-sm font-bold text-gray-700 mb-2">KCSE Index Number</label>
            <input type="text" name="kcse_index" value="{{ $formData['academic']['kcse_index'] ?? '' }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Enter your KCSE index number">
        </div>
    </div>

    {{-- KCSE Subject Grades --}}
    <div x-show="showSubjects" x-transition class="space-y-4">
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-200">
            <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-graduation-cap text-purple-600 mr-2"></i>
                Subject Grades (KCSE)
            </h4>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php
                    $subjects = [
                        'english' => 'English',
                        'kiswahili' => 'Kiswahili',
                        'mathematics' => 'Mathematics',
                        'biology' => 'Biology',
                        'physics' => 'Physics',
                        'chemistry' => 'Chemistry',
                        'computer_studies' => 'Computer Studies',
                        'home_science' => 'Home Science',
                        'geography' => 'Geography',
                        'agriculture' => 'Agriculture',
                        'commerce' => 'Commerce',
                        'business_studies' => 'Business Studies',
                    ];
                    
                    $grades = ['' => '-', 'A' => 'A', 'A-' => 'A-', 'B+' => 'B+', 'B' => 'B', 'B-' => 'B-', 'C+' => 'C+', 'C' => 'C', 'C-' => 'C-', 'D+' => 'D+', 'D' => 'D', 'D-' => 'D-', 'E' => 'E'];
                @endphp
                
                @foreach($subjects as $key => $subjectName)
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $subjectName }}</label>
                    <select name="subjects[{{ $key }}]" @change="calculateMeanGrade()" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">-</option>
                        @foreach($grades as $g => $label)
                            @if($g !== '')
                                <option value="{{ $g }}" {{ ($formData['academic']['subjects'][$key] ?? '') == $g ? 'selected' : '' }}>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6 flex items-center justify-between p-4 bg-white rounded-lg border border-purple-200">
                <div>
                    <p class="text-sm text-gray-600">Calculated Mean Grade</p>
                    <p class="text-xs text-gray-500">Auto-calculated from your grades</p>
                </div>
                <div class="text-right">
                    <input type="hidden" name="mean_grade" value="{{ $formData['academic']['mean_grade'] ?? '' }}">
                    <span class="text-3xl font-bold text-purple-600" id="calculatedMeanGrade">
                        {{ $formData['academic']['mean_grade'] ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Manual Mean Grade for KCSE if no subjects provided --}}
    <div x-show="showKcseFields && !showSubjects" x-transition>
        <label class="block text-sm font-bold text-gray-700 mb-2">KCSE Mean Grade</label>
        <select name="mean_grade" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            <option value="">Select your mean grade</option>
            @php $grades = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E']; @endphp
            @foreach($grades as $grade)
                <option value="{{ $grade }}" {{ ($formData['academic']['mean_grade'] ?? '') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
            @endforeach
        </select>
    </div>

    {{-- Year of Examination --}}
    <div x-show="showKcseFields" x-transition>
        <label class="block text-sm font-bold text-gray-700 mb-2">Year of Examination</label>
        <select name="examination_year" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            <option value="">Select Year</option>
            @for($year = 2025; $year >= 2015; $year--)
                <option value="{{ $year }}" {{ ($formData['academic']['examination_year'] ?? '') == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endfor
        </select>
    </div>

    {{-- GPA for Higher Education --}}
    <div x-show="showGradeFields" x-transition class="space-y-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">GPA / Score *</label>
            <input type="number" step="0.01" min="0" max="4.0" name="gpa" value="{{ $formData['academic']['gpa'] ?? '' }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="Enter your GPA (e.g., 3.5)">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Classification</label>
            <select name="classification" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <option value="">Select classification</option>
                <option value="first_class" {{ ($formData['academic']['classification'] ?? '') == 'first_class' ? 'selected' : '' }}>First Class Honours</option>
                <option value="upper_second" {{ ($formData['academic']['classification'] ?? '') == 'upper_second' ? 'selected' : '' }}>Upper Second Class (2.1)</option>
                <option value="lower_second" {{ ($formData['academic']['classification'] ?? '') == 'lower_second' ? 'selected' : '' }}>Lower Second Class (2.2)</option>
                <option value="pass" {{ ($formData['academic']['classification'] ?? '') == 'pass' ? 'selected' : '' }}>Pass</option>
                <option value="distinction" {{ ($formData['academic']['classification'] ?? '') == 'distinction' ? 'selected' : '' }}>Distinction</option>
                <option value="credit" {{ ($formData['academic']['classification'] ?? '') == 'credit' ? 'selected' : '' }}>Credit</option>
            </select>
        </div>
    </div>

    {{-- Additional Info --}}
    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
            <i class="fas fa-info-circle text-gray-500 mr-2"></i>
            Additional Qualifications
        </h4>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Any Other Relevant Qualifications</label>
            <textarea name="other_qualifications" rows="3" 
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                placeholder="List any other relevant certifications, short courses, or professional qualifications...">{{ $formData['academic']['other_qualifications'] ?? '' }}</textarea>
        </div>
    </div>
</div>