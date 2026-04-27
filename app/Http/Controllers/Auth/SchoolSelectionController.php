<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SchoolSelectionController extends Controller
{
    public function index()
    {
        try {
            $schools = School::where('status', 'active')
                ->orderBy('name')
                ->get();

            $schoolsData = $schools->map(function ($school) {
                return [
                    'id' => $school->id,
                    'name' => $school->name,
                    'slug' => $school->slug,
                    'logo' => $school->logo ?? null,
                    'primary_color' => $school->primary_color ?? '#7C3AED',
                    'domain' => $this->resolveSchoolDomain($school),
                ];
            });

            return response()->json([
                'schools' => $schoolsData,
                'count' => $schools->count()
            ]);
        } catch (\Exception $e) {
            Log::error('SchoolSelectionController error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'schools' => []
            ], 500);
        }
    }

    public function select(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'action' => 'required|in:login,register',
        ]);

        $school = School::where('id', $request->school_id)
            ->where('status', 'active')
            ->firstOrFail();

        $domain = $this->resolveSchoolDomain($school);
        $protocol = config('app.https') ? 'https' : 'http';

        return response()->json([
            'redirect_url' => "{$protocol}://{$domain}",
            'school' => [
                'id' => $school->id,
                'name' => $school->name,
                'logo' => $school->logo ?? null,
                'primary_color' => $school->primary_color ?? '#7C3AED',
            ]
        ]);
    }

    protected function resolveSchoolDomain(School $school): string
    {
        $domain = SchoolDomain::where('school_id', $school->id)
            ->where('is_primary', true)
            ->first();

        if ($domain) {
            return $domain->domain;
        }

        $baseDomain = config('app.base_domain') ?? config('app.domain');
        
        if ($baseDomain && $school->subdomain) {
            return "{$school->subdomain}.{$baseDomain}";
        }

        if ($school->domain) {
            return $school->domain;
        }

        return "{$school->slug}." . config('app.url', '127.0.0.1:8000');
    }
}