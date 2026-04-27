<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Program;
use App\Models\Intake;
use App\Models\Student;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LandingPageController extends Controller
{
    protected SettingsService $settings;

    public function __construct()
    {
        $this->settings = new SettingsService(null);
    }

    public function index(Request $request)
    {
        $systemName = system_setting('app_name') ?: system_setting('system_name', 'Admission Portal');
        $systemLogo = system_setting('system_logo');
        $tagline = system_setting('tagline', 'Streamline Admissions for Schools, Colleges & Universities');
        $primaryColor = system_setting('primary_color', '#101092');
        $contactEmail = system_setting('contact_email', 'info@example.com');
        $contactPhone = system_setting('contact_phone', '+254700000000');
        $footerText = system_setting('footer_text', '© ' . date('Y') . ' ' . $systemName . '. Powered by Duncowebsolutions');
        $showFooterBranding = system_setting('show_footer_branding', true);
        
        $schools = School::where('status', 'active')
            ->latest()
            ->take(12)
            ->get(['id', 'name', 'logo', 'primary_color', 'secondary_color']);
        
        $programs = Program::withoutGlobalScopes()
            ->where('is_active', true)
            ->with('department')
            ->orderBy('level')
            ->orderBy('name')
            ->limit(8)
            ->get();
        
        $studentCount = Student::withoutGlobalScopes()->count();
        $programCount = Program::withoutGlobalScopes()->where('is_active', true)->count();
        $schoolCount = School::where('status', 'active')->count();
        $applicationCount = \App\Models\Application::withoutGlobalScopes()->count();
        
        $successRate = 0;
        if ($applicationCount > 0) {
            $approvedCount = \App\Models\Application::withoutGlobalScopes()->where('status', 'approved')->count();
            $successRate = round(($approvedCount / $applicationCount) * 100);
        }
        
        $stats = [
            'students' => $studentCount,
            'programs' => $programCount,
            'schools' => $schoolCount,
            'applications' => $applicationCount,
            'success_rate' => $successRate . '%',
        ];
        
        $currentYear = date('Y');
        $admissionsOpen = system_setting('admissions_open', true);
        
        $testimonials = [];
        
        return view('welcome', compact(
            'systemName',
            'systemLogo',
            'tagline',
            'primaryColor',
            'contactEmail',
            'contactPhone',
            'footerText',
            'showFooterBranding',
            'schools',
            'programs',
            'stats',
            'currentYear',
            'admissionsOpen',
            'testimonials'
        ));
    }

    public function contact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        try {
            $contactEmail = system_setting('contact_email', 'info@example.com');
            
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
            ];

            try {
                Mail::raw(
                    "Name: {$data['name']}\nEmail: {$data['email']}\n\nMessage:\n{$data['message']}",
                    function ($message) use ($contactEmail, $data) {
                        $message->to($contactEmail)
                            ->subject('New Contact Form Submission')
                            ->replyTo($data['email']);
                    }
                );
            } catch (\Exception $mailException) {
                \Log::warning('Contact form email failed: ' . $mailException->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'Thank you for your message! We will get back to you soon.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send message. Please try again.'], 500);
        }
    }
}
