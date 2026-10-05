<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Models\AdmissionLetter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function application(Request $request, Application $application): JsonResponse|Response
    {
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $formData = is_array($application->form_data) ? $application->form_data : [];
        
        $pdf = PDF::loadView('student.exports.application-pdf', [
            'application' => $application,
            'formData' => $formData,
        ]);
        $pdf->setPaper('A4');
        $filename = 'application-' . $application->application_number . '.pdf';
        
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function paymentReceipt(Request $request, Payment $payment): JsonResponse|Response
    {
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $pdf = PDF::loadView('student.exports.payment-receipt', [
            'payment' => $payment,
        ]);
        $pdf->setPaper('A4');
        $filename = 'receipt-' . $payment->receipt_number . '.pdf';
        
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function admissionLetter(Request $request, AdmissionLetter $letter): JsonResponse|Response
    {
        $letter->load('application.user');
        
        if ($letter->application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!$letter->pdf_path || !Storage::disk('public')->exists($letter->pdf_path)) {
            return response()->json([
                'success' => false,
                'message' => 'PDF not found',
            ], 404);
        }

        $path = Storage::disk('public')->path($letter->pdf_path);
        $content = file_get_contents($path);
        $filename = 'admission-letter-' . $letter->letter_number . '.pdf';
        
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function admissionLetterPreview(Request $request, AdmissionLetter $letter): JsonResponse|Response
    {
        $letter->load(['application.student', 'application.program', 'application.user', 'application.school']);
        
        if ($letter->application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $pdf = PDF::loadView('student.exports.admission-letter', [
            'letter' => $letter,
            'school' => $letter->application->school,
        ]);
        $pdf->setPaper('A5', 'portrait');
        $filename = 'admission-letter-' . $letter->letter_number . '.pdf';
        
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function applicationPdfUrl(Request $request, Application $application): JsonResponse
    {
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $url = route('student.application.pdf', $application->id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => $url,
                'filename' => 'application-' . $application->application_number . '.pdf',
            ],
        ]);
    }

    public function paymentReceiptUrl(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $url = route('student.payment.receipt', $payment->id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => $url,
                'filename' => 'receipt-' . $payment->receipt_number . '.pdf',
            ],
        ]);
    }

    public function admissionLetterUrl(Request $request, AdmissionLetter $letter): JsonResponse
    {
        $letter->load('application.user');
        
        if ($letter->application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!$letter->pdf_path) {
            return response()->json([
                'success' => false,
                'message' => 'PDF not generated yet',
            ], 404);
        }

        $url = route('student.offers.download', $letter->id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => $url,
                'filename' => 'admission-letter-' . $letter->letter_number . '.pdf',
            ],
        ]);
    }
}