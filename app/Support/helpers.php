<?php

use App\Models\School;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;

if (!function_exists('toastr')) {
    function toastr()
    {
        return new class {
            public function success($message, $title = null) { $this->message('success', $message, $title); return $this; }
            public function error($message, $title = null) { $this->message('error', $message, $title); return $this; }
            public function warning($message, $title = null) { $this->message('warning', $message, $title); return $this; }
            public function info($message, $title = null) { $this->message('info', $message, $title); return $this; }
            private function message($type, $message, $title) { Session::flash('toastr', compact('type', 'message', 'title')); }
        };
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        $schoolId = School::getCurrentId();

        if ($schoolId) {
            $school = School::find($schoolId);
            $value = $school?->getConfig($key);
            return $value !== null ? $value : $default;
        }

        return Setting::get($key, $default);
    }
}

if (!function_exists('system_setting')) {
    function system_setting($key, $default = null)
    {
        return Setting::withoutGlobalScope(\App\Scopes\SchoolScope::class)
            ->where('key', $key)
            ->whereNull('school_id')
            ->value('value') ?? $default;
    }
}

if (!function_exists('school_setting')) {
    function school_setting($key, $default = null)
    {
        $school = current_school();

        if (!$school) {
            return $default;
        }

        return $school->getConfig($key, $default);
    }
}

if (!function_exists('contact_setting')) {
    function contact_setting($key, $default = null)
    {
        $school = current_school();
        
        if ($school) {
            $schoolValue = $school->getConfig($key);
            if ($schoolValue) {
                return $schoolValue;
            }
        }
        
        return system_setting($key, $default);
    }
}

if (!function_exists('app_footer')) {
    function app_footer(): ?string
    {
        $school = current_school();

        if ($school) {
            if ($school->enable_white_label) {
                return null;
            }

            if ($school->custom_footer_text) {
                return e($school->custom_footer_text);
            }
        }

        if (!system_setting('show_footer_branding', true)) {
            return null;
        }

        $footerText = system_setting('footer_text');

        if ($footerText) {
            return e($footerText);
        }

        $schoolName = $school?->name ?? system_setting('system_name', config('app.name'));

        return '© ' . date('Y') . ' ' . e($schoolName) . '. Powered by Duncowebsolutions';
    }
}

if (!function_exists('app_logo')) {
    function app_logo(): string
    {
        $school = current_school();

        if ($school && $school->logo) {
            return asset("storage/{$school->logo}");
        }

        $systemLogo = system_setting('system_logo');
        if ($systemLogo) {
            return asset("storage/{$systemLogo}");
        }

        return asset('images/logo.png');
    }
}

if (!function_exists('app_favicon')) {
    function app_favicon(): string
    {
        $school = current_school();

        if ($school && $school->favicon) {
            return asset("storage/{$school->favicon}");
        }

        $systemFavicon = system_setting('favicon');
        if ($systemFavicon) {
            return asset("storage/{$systemFavicon}");
        }

        return asset('favicon.ico');
    }
}

if (!function_exists('app_name')) {
    function app_name(): string
    {
        $school = current_school();

        if ($school) {
            return $school->name;
        }

        return system_setting('app_name') ?: system_setting('system_name', config('app.name'));
    }
}

if (!function_exists('primary_color')) {
    function primary_color(): string
    {
        $school = current_school();

        if ($school) {
            return $school->primary_color ?? system_setting('primary_color', '#101092');
        }

        return system_setting('primary_color', '#101092');
    }
}

if (!function_exists('setting_set')) {
    function setting_set($key, $value, $group = 'general')
    {
        $schoolId = School::getCurrentId();

        if ($schoolId) {
            $school = School::find($schoolId);
            $school?->setConfig($key, $value, $group);
            return;
        }

        Setting::set($key, $value, $group);
    }
}

if (!function_exists('current_school')) {
    function current_school(): ?School
    {
        if (app()->bound('current_school')) {
            return app()->make('current_school');
        }

        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($user->hasRole('super_admin')) {
            $impersonatingSchoolId = session('impersonating_school_id');
            if ($impersonatingSchoolId) {
                return School::find($impersonatingSchoolId);
            }
            return null;
        }

        return $user->school;
    }
}

if (!function_exists('current_school_id')) {
    function current_school_id(): ?int
    {
        return current_school()?->id;
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('super_admin');
    }
}

if (!function_exists('can_access_all_schools')) {
    function can_access_all_schools(): bool
    {
        return is_super_admin();
    }
}

if (!function_exists('seo_title')) {
    function seo_title(): string
    {
        $school = current_school();
        
        $title = null;
        if ($school) {
            $title = $school->getConfig('meta_title');
        }
        
        return $title ?? system_setting('meta_title', app_name() . ' - Admission Portal');
    }
}

if (!function_exists('seo_description')) {
    function seo_description(): string
    {
        $school = current_school();
        
        $description = null;
        if ($school) {
            $description = $school->getConfig('meta_description');
        }
        
        return $description ?? system_setting('meta_description', 'Apply to ' . app_name() . ' easily online. Streamlined admission process for students.');
    }
}

if (!function_exists('seo_keywords')) {
    function seo_keywords(): string
    {
        $school = current_school();
        
        $keywords = null;
        if ($school) {
            $keywords = $school->getConfig('meta_keywords');
        }
        
        return $keywords ?? system_setting('meta_keywords', 'admissions, online application, ' . app_name());
    }
}

if (!function_exists('seo_og_title')) {
    function seo_og_title(): string
    {
        $school = current_school();
        
        $title = null;
        if ($school) {
            $title = $school->getConfig('og_title');
        }
        
        return $title ?? system_setting('og_title', seo_title());
    }
}

if (!function_exists('seo_og_description')) {
    function seo_og_description(): string
    {
        $school = current_school();
        
        $description = null;
        if ($school) {
            $description = $school->getConfig('og_description');
        }
        
        return $description ?? system_setting('og_description', seo_description());
    }
}

if (!function_exists('seo_og_image')) {
    function seo_og_image(): string
    {
        $school = current_school();
        
        $image = null;
        if ($school && $school->logo) {
            $image = $school->logoUrl;
        }
        
        return $image ?? system_setting('og_image', app_logo());
    }
}

if (!function_exists('seo_twitter_card')) {
    function seo_twitter_card(): string
    {
        return system_setting('twitter_card', 'summary_large_image');
    }
}

if (!function_exists('seo_canonical_url')) {
    function seo_canonical_url(): string
    {
        $canonical = system_setting('canonical_url');
        return $canonical ?? url('/');
    }
}

if (!function_exists('seo_json_ld')) {
    function seo_json_ld(): string
    {
        $school = current_school();
        $organizationName = $school?->name ?? app_name();
        $organizationUrl = $school?->website ?? url('/');
        $organizationEmail = $school?->email ?? system_setting('contact_email');
        $organizationPhone = $school?->phone ?? system_setting('contact_phone');
        $organizationAddress = $school?->address;
        
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => seo_canonical_url() . '/#organization',
                    'name' => $organizationName,
                    'url' => $organizationUrl,
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => seo_canonical_url() . '/#website',
                    'url' => seo_canonical_url(),
                    'name' => $organizationName,
                    'potentialAction' => [
                        '@type' => 'SearchAction',
                        'target' => [
                            '@type' => 'EntryPoint',
                            'urlTemplate' => seo_canonical_url() . '/search?q={search_term_string}'
                        ],
                        'query-input' => 'required name=search_term_string'
                    ]
                ]
            ]
        ];
        
        if ($organizationEmail || $organizationPhone || $organizationAddress) {
            $contactPoint = ['@type' => 'ContactPoint'];
            if ($organizationPhone) {
                $contactPoint['telephone'] = $organizationPhone;
                $contactPoint['contactType'] = 'customer service';
            }
            if ($organizationEmail) {
                $contactPoint['email'] = $organizationEmail;
            }
            
            $schema['@graph'][] = [
                '@type' => 'EducationalOrganization',
                '@id' => seo_canonical_url() . '/#educationalOrganization',
                'name' => $organizationName,
                'url' => $organizationUrl,
                'contactPoint' => $contactPoint,
            ];
        } else {
            $schema['@graph'][] = [
                '@type' => 'EducationalOrganization',
                '@id' => seo_canonical_url() . '/#educationalOrganization',
                'name' => $organizationName,
                'url' => $organizationUrl,
            ];
        }
        
        return json_encode($schema, JSON_UNESCAPED_SLASHES);
    }
}
