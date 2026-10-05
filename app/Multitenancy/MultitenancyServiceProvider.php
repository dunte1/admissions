<?php

namespace App\Multitenancy;

use App\Models\School;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class MultitenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantManager::class, function ($app) {
            return new TenantManager();
        });
    }

    public function boot(): void
    {
        if (!Schema::hasTable('schools')) {
            return;
        }

        $this->bootTenantIdentification();
        $this->bootBranding();
        $this->bootGlobalScopes();
    }

    protected function bootTenantIdentification(): void
    {
        $this->app['router']->aliasMiddleware('tenant', \App\Http\Middleware\Multitenancy\IdentifyTenant::class);
        $this->app['router']->aliasMiddleware('tenant.scope', \App\Http\Middleware\Multitenancy\TenantScope::class);
        $this->app['router']->aliasMiddleware('tenant.auth', \App\Http\Middleware\Multitenancy\TenantAuth::class);
        $this->app['router']->aliasMiddleware('feature.access', \App\Http\Middleware\Multitenancy\CheckFeatureAccess::class);
        $this->app['router']->aliasMiddleware('super.admin', \App\Http\Middleware\Multitenancy\RequireSuperAdminGuard::class);
    }

    protected function bootBranding(): void
    {
        View::composer('*', function ($view) {
            $school = TenantManager::getCurrentSchool();
            
            if ($school) {
                $branding = BrandingService::load($school);
                
                $view->with([
                    'schoolBranding' => $branding['school'],
                    'schoolLogo' => $branding['logo'],
                    'schoolPrimaryColor' => $branding['primary_color'],
                    'schoolSecondaryColor' => $branding['secondary_color'],
                ]);
            }
        });
    }

    protected function bootGlobalScopes(): void
    {
        $models = [
            \App\Models\User::class,
            \App\Models\Application::class,
            \App\Models\Program::class,
            \App\Models\Intake::class,
            \App\Models\Payment::class,
            \App\Models\Document::class,
            \App\Models\AuditLog::class,
            \App\Models\ActivityLog::class,
            \App\Models\Setting::class,
            \App\Models\Faq::class,
            \App\Models\Inquiry::class,
            \App\Models\Conversation::class,
            \App\Models\Broadcast::class,
            \App\Models\BroadcastRecipient::class,
            \App\Models\InquiryReply::class,
            \App\Models\AIConversation::class,
            \App\Models\AIMessage::class,
            \App\Models\AILead::class,
            \App\Models\AIKnowledge::class,
            \App\Models\ChatHistory::class,
            \App\Models\Subscription::class,
            \App\Models\Backup::class,
            \App\Models\PaymentLog::class,
            \App\Models\AdmissionLetter::class,
            \App\Models\LetterTemplate::class,
        ];

        foreach ($models as $model) {
            $model::addGlobalScope(new \App\Scopes\SchoolScope());
        }
    }
}