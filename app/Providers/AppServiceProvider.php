<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\Application;
use App\Observers\ApplicationObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Application::observe(ApplicationObserver::class);

        Blade::component('layouts.admin', 'admin-layout');
        
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        Blade::if('hasrole', function ($roles) {
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });

        Blade::if('permission', function ($permission) {
            return auth()->check() && auth()->user()->can($permission);
        });

        Blade::if('haspermission', function ($permissions) {
            if (!auth()->check()) return false;
            $permissions = is_array($permissions) ? $permissions : func_get_args();
            return auth()->user()->hasAnyPermission($permissions);
        });

        Blade::if('superadmin', function () {
            return auth()->check() && auth()->user()->hasRole('super_admin');
        });

        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->isAdmin();
        });
    }
}
