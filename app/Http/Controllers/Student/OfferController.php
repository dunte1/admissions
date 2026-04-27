<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AdmissionLetter;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfferController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:student']);
    }

    public function index()
    {
        $user = auth()->user();
        $applications = $user->applications()
            ->where('status', 'approved')
            ->with(['admissionLetter' => fn($q) => $q->withoutGlobalScope(\App\Scopes\SchoolScope::class)->where('type', 'admission')])
            ->get();

        return view('student.offers.index', compact('applications', 'user'));
    }

    public function show($offer)
    {
        $letter = AdmissionLetter::withoutGlobalScope(\App\Scopes\SchoolScope::class)
            ->find($offer);
        
        if (!$letter) {
            abort(404, 'Offer not found');
        }
        
        $letter->load(['application.student', 'application.program', 'application.user']);
        
        if ($letter->application->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this offer');
        }

        return view('student.offers.show', compact('letter'));
    }

    public function accept(Request $request, $offer)
    {
        $letter = AdmissionLetter::withoutGlobalScope(\App\Scopes\SchoolScope::class)
            ->find($offer);
        
        if (!$letter) {
            abort(404, 'Offer not found');
        }
        
        $letter->load('application.user');
        
        if ($letter->application->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to respond to this offer');
        }
        
        if (!$letter->isPending()) {
            toastr()->error('This offer can no longer be accepted.');
            return back();
        }

        if ($letter->isExpired()) {
            toastr()->error('The response deadline has passed.');
            return back();
        }

        $letter->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        AuditLog::log('accept_offer', $letter->application, null, [
            'letter_number' => $letter->letter_number,
        ]);

        toastr()->success('Congratulations! You have accepted the offer. Further instructions will be sent to your email.');
        return redirect()->route('student.offers.index');
    }

    public function decline(Request $request, $offer)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);
        
        $letter = AdmissionLetter::withoutGlobalScope(\App\Scopes\SchoolScope::class)
            ->find($offer);
        
        if (!$letter) {
            abort(404, 'Offer not found');
        }
        
        $letter->load('application.user');

        if ($letter->application->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to respond to this offer');
        }
        
        if (!$letter->isPending()) {
            toastr()->error('This offer can no longer be declined.');
            return back();
        }

        $letter->update([
            'status' => 'declined',
            'responded_at' => now(),
            'remarks' => $request->reason,
        ]);

        AuditLog::log('decline_offer', $letter->application, null, [
            'letter_number' => $letter->letter_number,
            'reason' => $request->reason,
        ]);

        toastr()->info('You have declined the offer.');
        return redirect()->route('student.offers.index');
    }

    public function download($offer)
    {
        $letter = AdmissionLetter::withoutGlobalScope(\App\Scopes\SchoolScope::class)
            ->find($offer);
        
        if (!$letter) {
            abort(404, 'Offer not found');
        }
        
        $letter->load('application.user');

        if ($letter->application->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to download this offer');
        }
        
        if (!$letter->pdf_path || !Storage::disk('public')->exists($letter->pdf_path)) {
            abort(404, 'PDF not found');
        }

        return Storage::disk('public')->download($letter->pdf_path, "admission-letter-{$letter->letter_number}.pdf");
    }

    public function preview($offer)
    {
        $letter = AdmissionLetter::withoutGlobalScope(\App\Scopes\SchoolScope::class)
            ->find($offer);
        
        if (!$letter) {
            abort(404, 'Offer not found');
        }
        
        $letter->load(['application.student', 'application.program', 'application.user', 'application.school']);

        if ($letter->application->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this offer');
        }

        $school = $letter->application->school;
        $institution = $school?->name ?? setting('institution_name', 'Institution');
        $address = $school?->address ?? setting('address', '');
        $phone = $school?->phone ?? setting('phone', '');
        $email = $school?->email ?? setting('email', '');
        $website = $school?->website ?? '';

        $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(80)
            ->generate(route('letter.verify', $letter->letter_number)));

        return view('student.offers.preview', [
            'letter' => $letter,
            'school' => $school,
            'institution' => $institution,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'website' => $website,
            'qrCode' => $qrCode,
        ]);
    }
}
