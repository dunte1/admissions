<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Student;
use App\Models\Program;
use App\Models\School;
use App\Models\Document;
use App\Models\FormSection;
use App\Models\FormField;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    protected $fileUpload;

    public function __construct(FileUploadService $fileUpload)
    {
        $this->fileUpload = $fileUpload;
        $this->middleware(['auth', 'role:student']);
    }

    protected function getFormSections(string $step): array
    {
        $schoolId = Auth::user()->school_id;
        
        $section = FormSection::forSchoolSlug($schoolId, $step)
            ->active()
            ->with(['fields' => function ($q) {
                $q->active()->ordered();
            }])
            ->first();

        if (!$section) {
            return [];
        }

        return $section->activeFields->all();
    }

    protected function getDynamicValidationRules(string $step): array
    {
        // For custom blade forms, use step-specific validation
        $customFormRules = $this->getCustomFormValidationRules($step);
        if (!empty($customFormRules)) {
            return $customFormRules;
        }
        
        // Fallback to dynamic form fields (database-driven)
        $fields = $this->getFormSections($step);
        $rules = [];

        foreach ($fields as $field) {
            $rules[$field->key] = $field->getValidationRules();
        }

        return $rules;
    }
    
    protected function getCustomFormValidationRules(string $step): array
    {
        $rules = [];
        
        switch ($step) {
            case 'personal':
                $rules = [
                    'first_name' => 'required|string|max:100',
                    'last_name' => 'required|string|max:100',
                    'gender' => 'required|in:male,female',
                    'date_of_birth' => 'required|date',
                    'phone' => 'required|string|max:20',
                    'email' => 'required|email|max:255',
                    'county' => 'required|string|max:100',
                    'sub_county' => 'required|string|max:100',
                    'nationality' => 'required|string|max:100',
                ];
                break;
                
            case 'academic':
                $rules = [
                    'programme_id' => 'required',
                    'intake' => 'required',
                    'education_level' => 'required',
                    'institution_name' => 'required',
                    'year_from' => 'required|numeric',
                    'year_to' => 'required|numeric',
                ];
                break;
                
            case 'guardian':
                $rules = [
                    'guardian_name' => 'required',
                    'guardian_id_number' => 'required',
                    'relationship' => 'required',
                    'guardian_phone' => 'required',
                ];
                break;
                
            case 'documents':
                $rules = [];
                break;
                
            case 'financial':
                $rules = [
                    'sponsorship_type' => 'required',
                ];
                break;
                
            case 'declaration':
                $rules = [];
                break;
        }
        
        return $rules;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $applications = Application::where('user_id', $user->id)
            ->with(['program' => function ($query) {
                $query->withoutGlobalScopes();
            }])
            ->latest()
            ->get();

        $stats = [
            'total' => $applications->count(),
            'draft' => $applications->where('status', 'draft')->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'approved' => $applications->where('status', 'approved')->count(),
        ];

        return view('student.dashboard', compact('applications', 'stats'));
    }

    public function create()
    {
        $programs = Program::withoutGlobalScopes()
            ->where('is_active', true)
            ->with('department')
            ->orderBy('level')
            ->orderBy('name')
            ->get()
            ->map(function ($program) {
                $admittedCount = \App\Models\Application::where('program_id', $program->id)
                    ->where('status', 'approved')
                    ->count();
                $program->admitted_count = $admittedCount;
                $program->available_slots = $program->capacity ? ($program->capacity - $admittedCount) : null;
                $program->is_full = $program->capacity && $admittedCount >= $program->capacity;
                return $program;
            });
        
        $applicationFee = school_setting('application_fee', system_setting('application_fee', 2000));
        
        if (session('draft_application_id')) {
            $draft = Application::find(session('draft_application_id'));
            if ($draft && $draft->user_id === Auth::id()) {
                return redirect()->route('student.application.form', ['step' => $draft->current_step ?? 'personal']);
            }
        }

        return view('student.create', compact('programs', 'applicationFee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id_1' => 'required|exists:programs,id',
            'program_id_2' => 'nullable|exists:programs,id|different:program_id_1',
            'program_id_3' => 'nullable|exists:programs,id|different:program_id_1|different:program_id_2',
        ]);

        // Check for duplicate applications to the same program
        $existingApplication = Application::where('user_id', Auth::id())
            ->where('program_id', $request->program_id_1)
            ->whereIn('status', ['draft', 'pending'])
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You already have an active application for this program. Please continue with that application or submit it first.');
        }

        DB::transaction(function () use ($request) {
            // Check if student record already exists or create new
            $student = Student::firstOrCreate(
                ['user_id' => Auth::id()],
                [
                    'school_id' => Auth::user()->school_id,
                    'first_name' => Auth::user()->first_name,
                    'last_name' => Auth::user()->last_name,
                ]
            );

            $applicationData = [
                'user_id' => Auth::id(),
                'school_id' => Auth::user()->school_id,
                'student_id' => $student->id,
                'program_id' => $request->program_id_1,
                'program_choice_2' => $request->program_id_2,
                'program_choice_3' => $request->program_id_3,
                'application_number' => Application::generateNumber(),
                'status' => 'draft',
                'current_step' => 'personal',
            ];

            $application = Application::create($applicationData);

            // Store program choices in form_data as well for reference
            $application->update([
                'form_data' => [
                    'program_choices' => [
                        'first' => $request->program_id_1,
                        'second' => $request->program_id_2,
                        'third' => $request->program_id_3,
                    ],
                ],
            ]);

            session(['draft_application_id' => $application->id]);
        });

        return redirect()->route('student.application.form', ['step' => 'personal'])
            ->with('success', 'Application started! Please complete all required sections.');
    }

    public function showForm($step)
    {
        $validSteps = ['personal', 'academic', 'guardian', 'documents', 'financial', 'declaration'];
        
        if (!in_array($step, $validSteps)) {
            return redirect()->route('student.application.form', ['step' => 'personal']);
        }

        $user = Auth::user();
        $application = Application::where('user_id', $user->id)
            ->where('status', 'draft')
            ->latest()
            ->firstOrFail();

        $student = $application->student;
        $formData = is_array($application->form_data) ? $application->form_data : [];

        $stepIndex = array_search($step, $validSteps);
        $previousStep = $stepIndex > 0 ? $validSteps[$stepIndex - 1] : null;

        $steps = [
            'personal' => __('labels.personal_info'),
            'academic' => __('labels.academic_background'),
            'guardian' => __('labels.guardian_info'),
            'documents' => __('labels.documents'),
            'financial' => __('labels.financial_info'),
            'declaration' => __('labels.declaration'),
        ];

        $completedSteps = collect($validSteps)->filter(function ($s) use ($formData) {
            return !empty($formData[$s]) && is_array($formData[$s]);
        });

        $completionPercentage = count($completedSteps) > 0 ? round((count($completedSteps) / count($validSteps)) * 100) : 0;

        $formFields = $this->getFormSections($step);
        $sectionData = $formData[$step] ?? [];

        return view('student.application', [
            'application' => $application,
            'student' => $student,
            'step' => $step,
            'stepLabel' => $steps[$step],
            'previousStep' => $previousStep,
            'steps' => $steps,
            'currentStep' => $step,
            'completedSteps' => $completedSteps,
            'completionPercentage' => $completionPercentage,
            'formData' => $formData,
            'formFields' => $formFields,
            'sectionData' => $sectionData,
        ]);
    }

    public function saveForm(Request $request, $step)
    {
        try {
            $user = Auth::user();
            $application = Application::where('user_id', $user->id)
                ->where('status', 'draft')
                ->latest()
                ->firstOrFail();

            $dynamicRules = $this->getDynamicValidationRules($step);
            
            // Skip validation for documents, financial, and declaration (handled separately)
            if ($step !== 'documents' && $step !== 'financial' && $step !== 'declaration') {
                $request->validate($dynamicRules);
            }

            $formData = is_array($application->form_data) ? $application->form_data : [];
        
        $inputData = $request->except(['_token']);
        
        if ($step === 'personal') {
            $inputData['primary_phone'] = $inputData['phone'] ?? $user->phone ?? null;
            $inputData['primary_email'] = $inputData['email'] ?? $user->email ?? null;
            $inputData['country'] = $inputData['country'] ?? 'Kenya';
        }
        
        // Financial step custom validation
        if ($step === 'financial') {
            $sponsorshipType = $inputData['sponsorship_type'] ?? 'self';
            $payOption = $inputData['pay_option'] ?? 'full';
            
            // Sponsor fields required when not self-sponsored
            if ($sponsorshipType !== 'self') {
                if (empty($inputData['sponsor_name'])) {
                    return back()->with('error', 'Sponsor Name is required.')->withInput($inputData);
                }
                if (empty($inputData['sponsor_phone'])) {
                    return back()->with('error', 'Sponsor Phone is required.')->withInput($inputData);
                }
            }
            
            // Full payment validation
            if ($payOption === 'full') {
                if (empty($inputData['full_payment_reference'])) {
                    return back()->with('error', 'Transaction Reference Number is required.')->withInput($inputData);
                }
            }
            
            // Per-fee payment validation
            if ($payOption === 'per_fee') {
                if (($inputData['application_fee_paid'] ?? 'no') === 'yes') {
                    if (empty($inputData['application_fee_reference'])) {
                        return back()->with('error', 'Application Fee Reference is required.')->withInput($inputData);
                    }
                }
                if (($inputData['commitment_fee_paid'] ?? 'no') === 'yes') {
                    if (empty($inputData['commitment_fee_reference'])) {
                        return back()->with('error', 'Commitment Fee Reference is required.')->withInput($inputData);
                    }
                }
            }
        }
        
        $formData[$step] = $inputData;

        if ($step === 'documents') {
            $this->processDocumentUploads($request, $application);
        }

        if ($step === 'declaration') {
            $agreedAccuracy = $request->has('agreed_accuracy');
            $agreedRules = $request->has('agreed_rules');
            $agreedFees = $request->has('agreed_fees');
            $agreedDocuments = $request->has('agreed_documents');
            $agreedCommunication = $request->has('agreed_communication');
            
            if (!$agreedAccuracy || !$agreedRules || !$agreedFees || !$agreedDocuments || !$agreedCommunication) {
                return back()->with('error', 'You must agree to all terms and conditions to proceed.');
            }
            
            DB::transaction(function () use ($application, $formData, $user) {
                $application->update([
                    'form_data' => $formData,
                    'status' => 'pending',
                    'submitted_at' => now(),
                    'current_step' => 'submitted',
                ]);
            });

            $user->notify(new \App\Notifications\ApplicationSubmitted($application));

            return redirect()->route('student.dashboard')->with('success', 'Application submitted successfully! Application Number: ' . $application->application_number);
        }

        $nextSteps = [
            'personal' => 'academic',
            'academic' => 'guardian',
            'guardian' => 'documents',
            'documents' => 'financial',
            'financial' => 'declaration',
        ];
        
        $nextStep = $nextSteps[$step] ?? 'financial';
        
        $application->update([
            'form_data' => $formData,
            'current_step' => $nextStep,
        ]);
        
        return redirect()->route('student.application.form', ['step' => $nextStep])->with('saved', 'Progress saved!');
        } catch (\Exception $e) {
            \Log::error('SaveForm error: ' . $e->getMessage());
            return back()->with('error', 'Error saving form: ' . $e->getMessage())->withInput();
        }
    }

    protected function processDocumentUploads(Request $request, Application $application): void
    {
        // Handle file uploads from custom blade forms
        $fileFields = ['id_document', 'kcse_certificate', 'passport_photo', 'birth_certificate', 
                       'kcse_result_slip', 'prior_certificate', 'sponsorship_letter', 'medical_certificate'];
        
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                
                // Determine file type based on field name
                $typeMap = [
                    'id_document' => 'national_id',
                    'kcse_certificate' => 'kcse_certificate',
                    'passport_photo' => 'passport_photo',
                    'birth_certificate' => 'birth_certificate',
                    'kcse_result_slip' => 'kcse_result_slip',
                    'prior_certificate' => 'prior_certificate',
                    'sponsorship_letter' => 'sponsorship_letter',
                    'medical_certificate' => 'medical_certificate',
                ];
                
                $docType = $typeMap[$field] ?? $field;
                
                // Upload file
                $storedPath = $this->fileUpload->uploadFile($file, 'documents/' . $application->id);
                
                $application->documents()->updateOrCreate(
                    ['type' => $docType],
                    [
                        'original_name' => $file->getClientOriginalName(),
                        'stored_path' => $storedPath,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'status' => 'pending',
                    ]
                );
            }
        }
    }

    public function review(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $formData = is_array($application->form_data) ? $application->form_data : [];
        $student = $application->student;

        return view('student.review', compact('application', 'formData', 'student'));
    }

    public function submit(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $formData = is_array($application->form_data) ? $application->form_data : [];

        if (count($formData) < 3) {
            return redirect()->back()->with('error', 'Please complete all required sections.');
        }

        DB::transaction(function () use ($application) {
            $application->update([
                'status' => 'pending',
                'submitted_at' => now(),
            ]);
        });

        return redirect()->route('student.dashboard')->with('success', 'Application submitted successfully!');
    }

    public function show(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $application->load(['program' => function ($query) {
            $query->withoutGlobalScopes();
        }, 'documents', 'payments']);

        return view('student.application-show', compact('application'));
    }

    public function downloadPdf(Application $application)
    {
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        $application->load(['student', 'program', 'user', 'documents', 'payments']);
        $formData = is_array($application->form_data) ? $application->form_data : [];
        
        $pdf = \PDF::loadView('student.exports.application-pdf', compact('application', 'formData'));
        $pdf->setPaper('A4');
        
        return $pdf->download('application-' . $application->application_number . '.pdf');
    }
}
