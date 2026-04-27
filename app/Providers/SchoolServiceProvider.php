<?php

namespace App\Providers;

use App\Models\School;
use Illuminate\Support\ServiceProvider;

class SchoolServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('current_school', function ($app) {
            $user = $app['auth']->user();

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
        });
    }

    public function boot(): void
    {
        //
    }
}