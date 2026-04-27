<?php

namespace App\Multitenancy\Middleware;

use App\Multitenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    protected array $featureMap = [
        'student.dashboard' => 'admissions',
        'student.application.' => 'admissions',
        'admin.dashboard' => 'admissions',
        'admin.applications.' => 'admissions',
        'admin.exams.' => 'online_exams',
        'admin.finance.' => 'finance',
        'admin.library.' => 'library',
        'admin.notifications.' => 'notifications',
        'ai.chat' => 'ai_chatbot',
        'admin.messages.' => 'messaging',
        'admin.documents.' => 'document_verification',
        'admin.admission-letters.' => 'admission_letters',
        'admin.analytics' => 'analytics',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        
        if (!$routeName) {
            return $next($request);
        }

        $feature = $this->resolveFeature($routeName);
        
        if (!$feature) {
            return $next($request);
        }

        if (!TenantManager::isFeatureActive($feature)) {
            return $this->handleFeatureDisabled($request, $feature);
        }

        return $next($request);
    }

    protected function resolveFeature(string $routeName): ?string
    {
        foreach ($this->featureMap as $pattern => $feature) {
            if (str_starts_with($routeName, rtrim($pattern, '.'))) {
                return $feature;
            }
        }

        return null;
    }

    protected function handleFeatureDisabled(Request $request, string $feature): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => "The '{$feature}' feature is currently disabled for your school.",
                'feature' => $feature,
            ], 403);
        }

        $featureNames = [
            'admissions' => 'Admissions Portal',
            'online_exams' => 'Online Exams',
            'finance' => 'Finance Module',
            'library' => 'Library Module',
            'notifications' => 'Notifications',
            'ai_chatbot' => 'AI Chatbot',
            'messaging' => 'Messaging',
            'document_verification' => 'Document Verification',
            'admission_letters' => 'Admission Letters',
            'analytics' => 'Analytics',
        ];

        $featureName = $featureNames[$feature] ?? $feature;

        if ($request->ajax()) {
            return response()->html(
                '<div class="alert alert-warning">The ' . $featureName . ' feature is currently disabled.</div>'
            );
        }

        return redirect()->back()->with('error', "The '{$featureName}' feature is currently disabled for your school.");
    }
}