<?php

namespace App\Multitenancy\Services;

use App\Models\School;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class BrandingService
{
    public static function load(?School $school = null): array
    {
        $school = $school ?? TenantManager::getCurrentSchool();
        
        if (!$school) {
            return self::getDefaultBranding();
        }

        return Cache::remember("branding:{$school->id}", 3600, function () use ($school) {
            return [
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'code' => $school->code,
                    'slug' => $school->slug,
                ],
                'logo' => $school->logoUrl,
                'favicon' => $school->faviconUrl,
                'primary_color' => $school->primaryColor,
                'secondary_color' => $school->secondaryColor,
                'contact' => [
                    'email' => $school->email,
                    'phone' => $school->phone,
                    'address' => $school->address,
                    'county' => $school->county,
                    'town' => $school->town,
                    'website' => $school->website,
                ],
                'social' => [
                    'facebook' => $school->facebook,
                    'twitter' => $school->twitter,
                    'instagram' => $school->instagram,
                    'linkedin' => $school->linkedin,
                ],
                'footer_text' => $school->shouldShowFooter() ? $school->footerText : null,
                'signature' => [
                    'name' => $school->signatureName,
                    'title' => $school->signatureTitle,
                    'image' => $school->signatureImageUrl,
                    'seal' => $school->sealImageUrl,
                ],
                'white_label' => $school->enable_white_label,
            ];
        });
    }

    public static function getDefaultBranding(): array
    {
        return [
            'school' => [
                'id' => null,
                'name' => config('app.name', 'Admission Portal'),
                'code' => null,
                'slug' => null,
            ],
            'logo' => asset('images/logo.png'),
            'favicon' => asset('favicon.ico'),
            'primary_color' => system_setting('primary_color', '#7C3AED'),
            'secondary_color' => system_setting('secondary_color', '#10B981'),
            'contact' => [
                'email' => system_setting('contact_email'),
                'phone' => system_setting('contact_phone'),
                'address' => system_setting('contact_address'),
                'county' => null,
                'town' => null,
                'website' => null,
            ],
            'social' => [
                'facebook' => null,
                'twitter' => null,
                'instagram' => null,
                'linkedin' => null,
            ],
            'footer_text' => system_setting('footer_text', '© ' . date('Y') . ' Powered by Duncowebsolutions'),
            'signature' => [
                'name' => null,
                'title' => null,
                'image' => null,
                'seal' => null,
            ],
            'white_label' => false,
        ];
    }

    public static function applyToView(): void
    {
        $branding = self::load();
        
        View::share('schoolBranding', $branding['school']);
        View::share('schoolLogo', $branding['logo']);
        View::share('schoolFavicon', $branding['favicon']);
        View::share('schoolPrimaryColor', $branding['primary_color']);
        View::share('schoolSecondaryColor', $branding['secondary_color']);
    }

    public static function getCssVariables(?School $school = null): string
    {
        $branding = self::load($school);
        
        $css = ":root {\n";
        $css .= "  --school-primary: {$branding['primary_color']};\n";
        $css .= "  --school-secondary: {$branding['secondary_color']};\n";
        $css .= "}\n";
        
        return $css;
    }

    public static function clearCache(?int $schoolId = null): void
    {
        if ($schoolId) {
            Cache::forget("branding:{$schoolId}");
        } else {
            Cache::flush();
        }
    }
}