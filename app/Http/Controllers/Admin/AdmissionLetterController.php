<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdmissionLetterMail;
use App\Models\AdmissionLetter;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\LetterTemplate;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdmissionLetterController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_applications');

        $query = AdmissionLetter::withoutGlobalScope(\App\Scopes\SchoolScope::class)
            ->with(['application.student', 'application.program']);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $letters = $query->latest()->paginate(20);

        return view('admin.admission-letters.index', compact('letters'));
    }

    public function create()
    {
        $this->authorize('approve_application');

        $applications = Application::where('status', 'approved')
            ->whereDoesntHave('admissionLetter', fn($q) => $q->withoutGlobalScope(\App\Scopes\SchoolScope::class)->whereIn('type', ['admission', 'calling']))
            ->with(['student', 'program'])
            ->get();

        return view('admin.admission-letters.create', compact('applications'));
    }

    public function store(Request $request)
    {
        $this->authorize('approve_application');

        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'type' => 'required|in:admission,rejection,provisional,deferral,calling',
            'response_deadline' => 'nullable|date|after:today',
            'additional_conditions' => 'nullable|string',
            'remarks' => 'nullable|string',
            // Interview fields for calling letter
            'interview_date' => 'nullable|required_if:type,calling|date',
            'interview_venue' => 'nullable|required_if:type,calling|string|max:255',
            'interview_instructions' => 'nullable|string',
        ]);

        $application = Application::findOrFail($request->application_id);
        
        if ($application->admissionLetter()->where('type', $request->type)->exists()) {
            toastr()->error('A letter of this type already exists for this application.');
            return back();
        }

        $letter = null;
        $maxAttempts = 10;
        $attempt = 0;
        
        while (!$letter && $attempt < $maxAttempts) {
            $attempt++;
            $letterNumber = AdmissionLetter::generateLetterNumber();
            
            if (AdmissionLetter::where('letter_number', $letterNumber)->exists()) {
                continue;
            }
            
            try {
                $letter = DB::transaction(function () use ($application, $request, $letterNumber) {
                    $letterData = [
                        'school_id' => $application->school_id,
                        'application_id' => $application->id,
                        'letter_number' => $letterNumber,
                        'type' => $request->type,
                        'status' => 'sent',
                        'issue_date' => now(),
                        'response_deadline' => $request->response_deadline,
                        'additional_conditions' => $request->additional_conditions,
                        'remarks' => $request->remarks,
                    ];
                    
                    // Add calling letter fields if type is calling
                    if ($request->type === 'calling') {
                        $letterData['interview_date'] = $request->interview_date;
                        $letterData['interview_venue'] = $request->interview_venue;
                        $letterData['interview_instructions'] = $request->interview_instructions;
                    }
                    
                    $letter = AdmissionLetter::create($letterData);

                        $letter->update(['pdf_path' => $this->generatePdf($letter)]);

                    $this->sendNotifications($letter);

                    AuditLog::log('generate_admission_letter', $application, null, [
                        'letter_type' => $request->type,
                        'letter_number' => $letter->letter_number,
                    ]);
                    
                    return $letter;
                });
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'letter_number')) {
                    $letter = null;
                    continue;
                }
                throw $e;
            }
        }

        if (!$letter) {
            toastr()->error('Failed to generate unique letter number. Please try again.');
            return back();
        }

        toastr()->success(__('Admission letter generated successfully.'));
        return redirect()->route('admin.admission-letters.index');
    }

    public function show(AdmissionLetter $letter)
    {
        $this->authorize('view_applications');

        $letter->load(['application.student', 'application.program', 'application.user']);

        return view('admin.admission-letters.show', compact('letter'));
    }

    public function download(AdmissionLetter $letter)
    {
        $this->authorize('view_applications');

        if (!$letter->pdf_path || !Storage::disk('public')->exists($letter->pdf_path)) {
            $letter->update(['pdf_path' => $this->generatePdf($letter)]);
        }

        return Storage::disk('public')->download($letter->pdf_path, "letter-{$letter->letter_number}.pdf");
    }

    public function resend(AdmissionLetter $letter)
    {
        $this->authorize('approve_application');

        if ($letter->status === 'accepted' || $letter->status === 'declined') {
            toastr()->error('Cannot resend a responded letter.');
            return back();
        }

        $letter->update([
            'status' => 'sent',
            'issue_date' => now(),
        ]);

        AuditLog::log('resend_admission_letter', $letter->application, null, [
            'letter_number' => $letter->letter_number,
        ]);

        toastr()->success('Letter resent successfully.');
        return back();
    }

    public function bulkCreate()
    {
        $this->authorize('approve_application');

        $type = request('type', 'admission');
        
        $applications = Application::where('status', 'approved')
            ->whereDoesntHave('admissionLetter', fn($q) => $q->withoutGlobalScope(\App\Scopes\SchoolScope::class)->where('type', $type))
            ->with(['student', 'program'])
            ->get();

        return view('admin.admission-letters.bulk-create', compact('applications', 'type'));
    }

    public function bulkStore(Request $request)
    {
        $this->authorize('approve_application');

        $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'exists:applications,id',
            'type' => 'required|in:admission,rejection,provisional,deferral,calling',
            'response_deadline' => 'nullable|date',
            'additional_conditions' => 'nullable|string',
            'remarks' => 'nullable|string',
            'interview_date' => 'nullable|required_if:type,calling',
            'interview_venue' => 'nullable|required_if:type,calling|string|max:255',
            'interview_instructions' => 'nullable|string',
        ]);

        $applications = Application::whereIn('id', $request->application_ids)->get();
        $created = 0;
        $failed = 0;
        $errors = [];

        foreach ($applications as $application) {
            if ($application->admissionLetter()->where('type', $request->type)->exists()) {
                $failed++;
                $errors[] = "Application {$application->application_number} already has a {$request->type} letter";
                continue;
            }

            $letter = null;
            $maxAttempts = 10;
            $attempt = 0;
            
            while (!$letter && $attempt < $maxAttempts) {
                $attempt++;
                $letterNumber = AdmissionLetter::generateLetterNumber();
                
                if (AdmissionLetter::where('letter_number', $letterNumber)->exists()) {
                    continue;
                }
                
                try {
                    $letter = DB::transaction(function () use ($application, $request, $letterNumber) {
                        $letterData = [
                            'school_id' => $application->school_id,
                            'application_id' => $application->id,
                            'letter_number' => $letterNumber,
                            'type' => $request->type,
                            'status' => 'sent',
                            'issue_date' => now(),
                            'response_deadline' => $request->response_deadline,
                            'additional_conditions' => $request->additional_conditions,
                            'remarks' => $request->remarks,
                        ];
                        
                        if ($request->type === 'calling') {
                            $letterData['interview_date'] = $request->interview_date;
                            $letterData['interview_venue'] = $request->interview_venue;
                            $letterData['interview_instructions'] = $request->interview_instructions;
                        }
                        
                        $letter = AdmissionLetter::create($letterData);
                        $letter->update(['pdf_path' => $this->generatePdf($letter)]);
                        $this->sendNotifications($letter);
                        
                        AuditLog::log('generate_admission_letter', $application, null, [
                            'letter_type' => $request->type,
                            'letter_number' => $letter->letter_number,
                            'bulk' => true,
                        ]);
                        
                        return $letter;
                    });
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'letter_number')) {
                        $letter = null;
                        continue;
                    }
                    throw $e;
                }
            }
            
            if ($letter) {
                $created++;
            } else {
                $failed++;
            }
        }

        $message = "Successfully created {$created} letter(s)";
        if ($failed > 0) {
            $message .= ", {$failed} failed";
            toastr()->warning($message);
        } else {
            toastr()->success($message);
        }

        return redirect()->route('admin.admission-letters.index');
    }

    protected function generatePdf(AdmissionLetter $letter): string
    {
        // Use GD instead of imagick for DomPDF
        if (!defined('DOMPDF_IMAGE_BACKEND')) {
            define('DOMPDF_IMAGE_BACKEND', 'gd');
        }
        
        $letter->load(['application.student', 'application.program', 'application.user', 'application.intake', 'school']);
        
        $school = $letter->application->school;
        
        $institution = $school?->name ?? Setting::get('institution_name', 'Institution');
        $address = $school?->address ?? Setting::get('address', '');
        $phone = $school?->phone ?? Setting::get('phone', '');
        $email = $school?->email ?? Setting::get('email', '');
        $website = $school?->website ?? Setting::get('website', '');
        
        $qrCode = base64_encode(\QrCode::format('svg')
            ->size(100)
            ->generate(route('letter.verify', $letter->letter_number)));

        // Check for custom template
        $template = LetterTemplate::where('type', $letter->type)
            ->where('school_id', $letter->school_id)
            ->where('is_active', true)
            ->first();

        $view = $template ? 'admin.admission-letters.custom-template' : 'admin.admission-letters.template';

        $variables = [
            '{{student_name}}' => $letter->application->student?->fullName() ?? ($letter->application->user?->first_name . ' ' . $letter->application->user?->last_name),
            '{{application_number}}' => $letter->application->application_number,
            '{{program}}' => $letter->application->program?->name,
            '{{institution}}' => $institution,
            '{{letter_number}}' => $letter->letter_number,
            '{{issue_date}}' => $letter->issue_date->format('F d, Y'),
            '{{response_deadline}}' => $letter->response_deadline?->format('F d, Y'),
            '{{interview_date}}' => $letter->interview_date?->format('F d, Y g:i A'),
            '{{interview_venue}}' => $letter->interview_venue,
            '{{address}}' => $address,
            '{{phone}}' => $phone,
            '{{email}}' => $email,
            '{{website}}' => $website,
        ];

        if ($template) {
            $header = str_replace(array_keys($variables), array_values($variables), $template->header_content ?? '');
            $body = str_replace(array_keys($variables), array_values($variables), $template->body_content ?? '');
            $footer = str_replace(array_keys($variables), array_values($variables), $template->footer_content ?? '');
        } else {
            $header = $body = $footer = null;
        }

        $pdf = PDF::loadView($view, [
            'letter' => $letter,
            'school' => $school,
            'institution' => $institution,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'website' => $website,
            'qrCode' => $qrCode,
            'customHeader' => $header ?? null,
            'customBody' => $body ?? null,
            'customFooter' => $footer ?? null,
        ]);

        $pdf->setPaper('a5', 'portrait');
        $pdf->setOption('enable-css', true);
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('margin-top', '10mm');
        $pdf->setOption('margin-right', '10mm');
        $pdf->setOption('margin-bottom', '10mm');
        $pdf->setOption('margin-left', '10mm');
        $pdf->setOption('dpi', 96);
        $pdf->setOption('isRemoteEnabled', true);

        $path = "admission-letters/{$letter->letter_number}.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    protected function sendLetterEmail(AdmissionLetter $letter): void
    {
        $letter->load(['application.user']);
        
        $user = $letter->application->user;
        
        if ($user && $user->email) {
            Mail::to($user->email)->send(new AdmissionLetterMail($letter));
        }
    }

    protected function sendLetterSms(AdmissionLetter $letter): void
    {
        $letter->load(['application.user', 'application.student', 'application.program', 'school']);
        
        $user = $letter->application->user;
        $school = $letter->application->school;
        
        if (!$user) return;

        $phone = $letter->application->student?->phone ?? $user->phone;
        
        if (!$phone) return;

        $message = match($letter->type) {
            'admission' => "Congratulations! You've been offered admission to {$letter->application->program?->name} at {$school?->name}. Login to view your letter. Reply deadline: {$letter->response_deadline?->format('d M Y')}",
            'calling' => "You've been invited for an interview at {$school?->name} on {$letter->interview_date?->format('d M Y, g:i A')} at {$letter->interview_venue}. Login for details.",
            'rejection' => "Your application to {$letter->application->program?->name} at {$school?->name} was not successful. Thank you for applying.",
            'provisional' => "Your admission to {$letter->application->program?->name} at {$school?->name} is provisional. Check your email for conditions.",
            'deferral' => "Your admission to {$letter->application->program?->name} at {$school?->name} has been deferred. Contact admissions for details.",
            default => "Update on your application {$letter->application->application_number} at {$school?->name}",
        };

        try {
            $vonage = app('vonage');
            $vonage->sender('ADMISSION')
                ->to($phone)
                ->send($message);
        } catch (\Exception $e) {
            \Log::warning("Failed to send SMS for letter {$letter->letter_number}: " . $e->getMessage());
        }
    }

    protected function sendNotifications(AdmissionLetter $letter): void
    {
        $this->sendLetterEmail($letter);
        
        $smsEnabled = system_setting('sms_notifications_enabled', false);
        if ($smsEnabled) {
            $this->sendLetterSms($letter);
        }
    }
}
