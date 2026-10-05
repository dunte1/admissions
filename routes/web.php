<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\Student\ApplicationController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\OfferController;
use App\Http\Controllers\Student\FaqController as StudentFaqController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\RoleAssignmentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\IntakeController;
use App\Http\Controllers\Admin\AdmissionLetterController;
use App\Http\Controllers\Admin\DocumentVerificationController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use App\Http\Controllers\SuperAdmin\GlobalSettingsController;
use App\Http\Controllers\SuperAdmin\GlobalProgramController;
use App\Http\Controllers\SuperAdmin\ApplicationsController;
use App\Http\Controllers\SuperAdmin\ReportsController;
use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperAdminSubscriptionController;
use App\Http\Controllers\SuperAdmin\FaqController as SuperAdminFaqController;
use App\Http\Controllers\SuperAdmin\AISettingsController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Messaging\ConversationController;
use App\Http\Controllers\Messaging\MessageController;
use App\Http\Controllers\AI\AIController;
use App\Http\Controllers\Admin\SchoolBroadcastController;
use Illuminate\Support\Facades\Route;

// SEO Routes
Route::get('/sitemap.xml', function () {
    $schools = \App\Models\School::where('status', 'active')->get();
    
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    $staticPages = [
        ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
        ['url' => route('login'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['url' => route('register'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['url' => route('super-admin.login'), 'priority' => '0.5', 'changefreq' => 'monthly'],
    ];
    
    foreach ($staticPages as $page) {
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . htmlspecialchars($page['url']) . '</loc>';
        $sitemap .= '<changefreq>' . $page['changefreq'] . '</changefreq>';
        $sitemap .= '<priority>' . $page['priority'] . '</priority>';
        $sitemap .= '</url>';
    }
    
    foreach ($schools as $school) {
        $schoolUrl = $school->domain ? 'https://' . $school->domain : route('home');
        $sitemap .= '<url>';
        $sitemap .= '<loc>' . htmlspecialchars($schoolUrl) . '</loc>';
        $sitemap .= '<changefreq>weekly</changefreq>';
        $sitemap .= '<priority>0.9</priority>';
        $sitemap .= '</url>';
    }
    
    $sitemap .= '</urlset>';
    
    return response($sitemap, 200, ['Content-Type' => 'application/xml', 'charset' => 'utf-8']);
})->name('sitemap');

Route::get('/robots.txt', function () {
    $robots = "User-agent: *\n";
    $robots .= "Allow: /\n";
    $robots .= "Disallow: /admin/\n";
    $robots .= "Disallow: /student/\n";
    $robots .= "Disallow: /super-admin/\n";
    $robots .= "Disallow: /api/\n";
    $robots .= "Sitemap: " . route('sitemap') . "\n";
    
    return response($robots, 200, ['Content-Type' => 'text/plain']);
})->name('robots');

// M-PESA Callbacks - Public, no auth, no CSRF required
Route::withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)->group(function () {
    Route::post('/mpesa/callback', [PaymentController::class, 'mpesaCallback'])->name('mpesa.callback');
    Route::post('/mpesa/validation', [PaymentController::class, 'mpesaValidation'])->name('mpesa.validation');
    Route::post('/mpesa/confirmation', [PaymentController::class, 'mpesaConfirmation'])->name('mpesa.confirmation');
    Route::get('/mpesa/result', [PaymentController::class, 'mpesaResult'])->name('mpesa.result');
});

Route::middleware('web')->group(function () {
    Route::get('/', [LandingPageController::class, 'index'])->name('home');
    Route::post('/contact', [LandingPageController::class, 'contact'])->name('contact');
    Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang');

    // Super Admin Auth Routes (separate portal at /admin-portal)
    Route::prefix('admin-portal')->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('/login', [\App\Http\Controllers\Auth\SuperAdminAuthController::class, 'showLogin'])->name('super-admin.login');
            Route::post('/login', [\App\Http\Controllers\Auth\SuperAdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('super-admin.login.submit');
            Route::get('/forgot-password', [\App\Http\Controllers\Auth\SuperAdminAuthController::class, 'showForgotPassword'])->name('super-admin.password.request');
            Route::post('/forgot-password', [\App\Http\Controllers\Auth\SuperAdminAuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('super-admin.password.email');
            Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\SuperAdminAuthController::class, 'showResetPassword'])->name('super-admin.password.reset');
            Route::post('/reset-password', [\App\Http\Controllers\Auth\SuperAdminAuthController::class, 'resetPassword'])->name('super-admin.password.update');
        });
        Route::middleware('auth')->group(function () {
            Route::post('/logout', [\App\Http\Controllers\Auth\SuperAdminAuthController::class, 'logout'])->name('super-admin.logout');
        });
    });

    Route::prefix('ai')->name('ai.')->group(function () {
        Route::post('/chat', [AIController::class, 'chat'])->name('chat');
        Route::post('/embed-chat', [AIController::class, 'embedChat'])->name('embed-chat');
        Route::post('/admission-flow', [AIController::class, 'admissionFlow'])->name('admission-flow');
        Route::get('/greeting', [AIController::class, 'greeting'])->name('greeting');
        Route::post('/clear', [AIController::class, 'clearHistory'])->name('clear');
        Route::get('/history', [AIController::class, 'history'])->name('history');
    });

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1');
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
        
        Route::get('/login-onepage', function() {
            return view('auth.login-onepage', ['selectedSchool' => null]);
        })->name('login.onepage');
    });

    Route::middleware('auth')->group(function () {
        Route::middleware('session.activity')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
            Route::post('/notifications/mark-all-read', function () {
                auth()->user()->unreadNotifications->markAsRead();
                return redirect()->back();
            })->name('notifications.mark-all-read');

            // Two-Factor Authentication
            Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
            Route::post('/two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
            Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
            Route::post('/two-factor/verify/submit', [TwoFactorController::class, 'verify'])->name('two-factor.verify.submit');
            Route::post('/two-factor/recovery', [TwoFactorController::class, 'verifyWithRecoveryCode'])->name('two-factor.recovery');
            Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
            Route::post('/two-factor/regenerate', [TwoFactorController::class, 'regenerateCodes'])->name('two-factor.regenerate');
            Route::post('/two-factor/cancel', function () {
                session()->forget(['two_factor_secret', 'two_factor_recovery_codes']);
                return redirect()->route('home');
            })->name('two-factor.cancel');

            // Session Management
            Route::get('/sessions', [SessionController::class, 'activeSessions'])->name('sessions');
            Route::post('/sessions/logout-all', [SessionController::class, 'logoutFromAllDevices'])->name('sessions.logout-all');
            Route::get('/sessions/{sessionId}/terminate', [SessionController::class, 'terminateSession'])->name('sessions.terminate');
        });
    });

    Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [ApplicationController::class, 'dashboard'])->name('dashboard');
        Route::get('/application/create', [ApplicationController::class, 'create'])->name('application.create');
        Route::post('/application', [ApplicationController::class, 'store'])->name('application.store');
        Route::get('/application/form/{step}', [ApplicationController::class, 'showForm'])->name('application.form');
        Route::post('/application/save/{step}', [ApplicationController::class, 'saveForm'])->name('application.save');
        Route::get('/application/review/{application}', [ApplicationController::class, 'review'])->name('application.review');
        Route::post('/application/submit/{application}', [ApplicationController::class, 'submit'])->name('application.submit');
        Route::get('/application/{application}', [ApplicationController::class, 'show'])->name('application.show');
        Route::get('/application/{application}/pdf', [ApplicationController::class, 'downloadPdf'])->name('application.pdf');
        Route::get('/payment/{application}', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/payment/{application}/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
        Route::post('/payment/{application}/manual', [PaymentController::class, 'submitManual'])->name('payment.manual');
        Route::get('/payment/{application}/callback', [PaymentController::class, 'callback'])->name('payment.callback');
        Route::get('/payment/{application}/status', [PaymentController::class, 'checkStatus'])->name('payment.check-status');
        Route::get('/payment/{application}/timeout', [PaymentController::class, 'timeout'])->name('payment.timeout');

        // API endpoint for M-PESA STK from form
        Route::post('/payment/initiate-mpesa', [PaymentController::class, 'initiateFromForm'])->name('payment.initiate-mpesa');
        Route::post('/payment/submit-bank', [PaymentController::class, 'submitBankFromForm'])->name('payment.submit-bank');
        Route::get('/payment/{payment}/receipt', [ReceiptController::class, 'studentDownload'])->name('payment.receipt');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/dark-mode', [ProfileController::class, 'toggleDarkMode'])->name('profile.dark-mode');
        Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
        Route::put('/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');
        Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [ProfileController::class, 'markNotificationRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all', [ProfileController::class, 'markAllRead'])->name('notifications.mark-all');

        Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
        Route::get('/offers/{offer}', [OfferController::class, 'show'])->name('offers.show');
        Route::post('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
        Route::post('/offers/{offer}/decline', [OfferController::class, 'decline'])->name('offers.decline');
        Route::get('/offers/{offer}/download', [OfferController::class, 'download'])->name('offers.download');
        Route::get('/offers/{offer}/preview', [OfferController::class, 'preview'])->name('offers.preview');

        Route::get('/faqs', [StudentFaqController::class, 'index'])->name('faqs.index');

        // Internal Messaging
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [ConversationController::class, 'index'])->name('index');
            Route::get('/create', [ConversationController::class, 'create'])->name('create');
            Route::post('/', [ConversationController::class, 'store'])->name('store');
            Route::get('/users', [ConversationController::class, 'getUsers'])->name('users');
            Route::post('/start', [ConversationController::class, 'startConversation'])->name('start');
            Route::get('/unread-count', [ConversationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/{conversation}', [ConversationController::class, 'show'])->name('show');
            Route::post('/{conversation}/read', [ConversationController::class, 'markAsRead'])->name('read');
            Route::post('/{conversation}/typing', [ConversationController::class, 'typing'])->name('typing');
            Route::get('/{conversation}/messages', [MessageController::class, 'index'])->name('messages.index');
            Route::post('/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
            Route::get('/{conversation}/messages/more', [MessageController::class, 'loadMore'])->name('messages.more');
            Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
            Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
            Route::post('/{conversation}/messages/read', [MessageController::class, 'markAsRead'])->name('messages.read');
        });
    });

    Route::middleware(['auth', 'role:support', 'school.scope', 'subscription'])->prefix('support')->name('support.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\SupportDashboardController::class, 'index'])->name('dashboard');
        Route::get('/applications', [\App\Http\Controllers\Admin\SupportDashboardController::class, 'applications'])->name('applications.index');
        Route::get('/applications/{application}', [\App\Http\Controllers\Admin\SupportDashboardController::class, 'applicationShow'])->name('applications.show');
        Route::get('/inquiries', [\App\Http\Controllers\Admin\SupportDashboardController::class, 'inquiries'])->name('inquiries.index');
        Route::get('/inquiries/{inquiry}', [\App\Http\Controllers\Admin\SupportDashboardController::class, 'inquiryShow'])->name('inquiries.show');
        Route::post('/inquiries/{inquiry}/reply', [\App\Http\Controllers\Admin\SupportDashboardController::class, 'inquiryReply'])->name('inquiries.reply');
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    });

    Route::middleware(['auth', 'role:admin,registrar,accountant,reviewer', 'school.scope', 'subscription'])->prefix('admin')->name('admin.')->group(function () {
        // Profile Routes
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/dark-mode', [\App\Http\Controllers\Admin\ProfileController::class, 'toggleDarkMode'])->name('profile.dark-mode');
        Route::get('/settings', [\App\Http\Controllers\Admin\ProfileController::class, 'settings'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\Admin\ProfileController::class, 'updateSettings'])->name('profile.settings.update');

        // Form Fields Management
        Route::middleware('permission:manage_form_fields')->group(function () {
            Route::get('/form-fields', [\App\Http\Controllers\Admin\FormFieldController::class, 'index'])->name('form-fields.index');
            Route::get('/form-fields/create-section', [\App\Http\Controllers\Admin\FormFieldController::class, 'createSection'])->name('form-fields.create-section');
            Route::post('/form-fields/sections', [\App\Http\Controllers\Admin\FormFieldController::class, 'storeSection'])->name('form-fields.store-section');
            Route::get('/form-fields/sections/{section}/edit', [\App\Http\Controllers\Admin\FormFieldController::class, 'editSection'])->name('form-fields.edit-section');
            Route::put('/form-fields/sections/{section}', [\App\Http\Controllers\Admin\FormFieldController::class, 'updateSection'])->name('form-fields.update-section');
            Route::delete('/form-fields/sections/{section}', [\App\Http\Controllers\Admin\FormFieldController::class, 'destroySection'])->name('form-fields.destroy-section');
            Route::post('/form-fields/sections/{section}/toggle', [\App\Http\Controllers\Admin\FormFieldController::class, 'toggleSection'])->name('form-fields.toggle-section');
            Route::get('/form-fields/sections/{section}/create-field', [\App\Http\Controllers\Admin\FormFieldController::class, 'createField'])->name('form-fields.create-field');
            Route::post('/form-fields/sections/{section}/fields', [\App\Http\Controllers\Admin\FormFieldController::class, 'storeField'])->name('form-fields.store-field');
            Route::get('/form-fields/fields/{field}/edit', [\App\Http\Controllers\Admin\FormFieldController::class, 'editField'])->name('form-fields.edit-field');
            Route::put('/form-fields/fields/{field}', [\App\Http\Controllers\Admin\FormFieldController::class, 'updateField'])->name('form-fields.update-field');
            Route::delete('/form-fields/fields/{field}', [\App\Http\Controllers\Admin\FormFieldController::class, 'destroyField'])->name('form-fields.destroy-field');
            Route::post('/form-fields/fields/{field}/toggle', [\App\Http\Controllers\Admin\FormFieldController::class, 'toggleField'])->name('form-fields.toggle-field');
            Route::post('/form-fields/reset', [\App\Http\Controllers\Admin\FormFieldController::class, 'resetToDefaults'])->name('form-fields.reset');
        });
        Route::get('/notifications', [\App\Http\Controllers\Admin\ProfileController::class, 'notifications'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\ProfileController::class, 'markNotificationRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all', [\App\Http\Controllers\Admin\ProfileController::class, 'markAllNotificationsRead'])->name('notifications.mark-all');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Accountant Dashboard
        Route::middleware('role:accountant')->group(function () {
            Route::get('/accountant', [\App\Http\Controllers\Admin\AccountantDashboardController::class, 'index'])->name('accountant.dashboard');
            Route::get('/accountant/payments', [\App\Http\Controllers\Admin\AccountantDashboardController::class, 'payments'])->name('accountant.payments');
            Route::get('/accountant/export', [\App\Http\Controllers\Admin\AccountantDashboardController::class, 'export'])->name('accountant.export');
        });

        // Reviewer Dashboard
        Route::middleware('role:reviewer')->group(function () {
            Route::get('/reviewer', [\App\Http\Controllers\Admin\ReviewerDashboardController::class, 'index'])->name('reviewer.dashboard');
            Route::get('/reviewer/applications', [\App\Http\Controllers\Admin\ReviewerDashboardController::class, 'applications'])->name('reviewer.applications');
            Route::post('/reviewer/applications/{application}/start', [\App\Http\Controllers\Admin\ReviewerDashboardController::class, 'startReview'])->name('reviewer.start');
            Route::post('/reviewer/applications/{application}/note', [\App\Http\Controllers\Admin\ReviewerDashboardController::class, 'addNote'])->name('reviewer.note');
        });

        Route::get('/applications', [DashboardController::class, 'applications'])->name('applications.index');
        Route::get('/applications/export', [DashboardController::class, 'export'])->name('applications.export');
        Route::get('/applications/{application}', [DashboardController::class, 'showApplication'])->name('applications.show');
        Route::get('/applications/{application}/pdf', [DashboardController::class, 'exportSinglePdf'])->name('applications.pdf');
        Route::get('/payments/{payment}/receipt', [ReceiptController::class, 'adminDownload'])->name('payments.receipt');
        Route::get('/payments/{payment}', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [\App\Http\Controllers\Admin\PaymentVerificationController::class, 'reject'])->name('payments.reject');
        Route::post('/applications/{application}/approve', [DashboardController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject', [DashboardController::class, 'reject'])->name('applications.reject');
        Route::post('/applications/{application}/request-info', [DashboardController::class, 'requestInfo'])->name('applications.request-info');
        Route::post('/applications/bulk-action', [DashboardController::class, 'bulkAction'])->name('applications.bulk-action');
        Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/create', [ProgramController::class, 'create'])->name('programs.create');
        Route::post('/programs', [ProgramController::class, 'store'])->name('programs.store');
        Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
        Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');
        Route::post('/programs/add-global', [ProgramController::class, 'addFromGlobal'])->name('programs.add-global');
        Route::post('/programs/{program}/toggle-status', [ProgramController::class, 'toggleStatus'])->name('programs.toggle-status');

        Route::middleware('permission:send_notifications')->group(function () {
            Route::get('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');
            Route::post('/notifications/send', [NotificationController::class, 'sendBulk'])->name('notifications.send-bulk');
        });

        // School Broadcasts
        Route::middleware('permission:send_notifications')->group(function () {
            Route::get('/broadcast', [SchoolBroadcastController::class, 'index'])->name('broadcast.index');
            Route::get('/broadcast/create', [SchoolBroadcastController::class, 'create'])->name('broadcast.create');
            Route::post('/broadcast', [SchoolBroadcastController::class, 'store'])->name('broadcast.store');
            Route::get('/broadcast/{broadcast}', [SchoolBroadcastController::class, 'show'])->name('broadcast.show');
            Route::post('/broadcast/{broadcast}/cancel', [SchoolBroadcastController::class, 'cancel'])->name('broadcast.cancel');
            Route::post('/broadcast/{broadcast}/resend', [SchoolBroadcastController::class, 'resend'])->name('broadcast.resend');
            Route::get('/broadcast/templates', [SchoolBroadcastController::class, 'templates'])->name('broadcast.templates');
            Route::get('/broadcast/templates/create', [SchoolBroadcastController::class, 'createTemplate'])->name('broadcast.templates.create');
            Route::post('/broadcast/templates', [SchoolBroadcastController::class, 'storeTemplate'])->name('broadcast.templates.store');
            Route::get('/broadcast/templates/{template}/edit', [SchoolBroadcastController::class, 'editTemplate'])->name('broadcast.templates.edit');
            Route::put('/broadcast/templates/{template}', [SchoolBroadcastController::class, 'updateTemplate'])->name('broadcast.templates.update');
        });

        // Internal Messaging
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [ConversationController::class, 'index'])->name('index');
            Route::get('/create', [ConversationController::class, 'create'])->name('create');
            Route::post('/', [ConversationController::class, 'store'])->name('store');
            Route::get('/users', [ConversationController::class, 'getUsers'])->name('users');
            Route::post('/start', [ConversationController::class, 'startConversation'])->name('start');
            Route::get('/unread-count', [ConversationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/unread-counts', [ConversationController::class, 'unreadCounts'])->name('unread-counts');
            Route::get('/{conversation}', [ConversationController::class, 'show'])->name('show');
            Route::post('/{conversation}/read', [ConversationController::class, 'markAsRead'])->name('read');
            Route::post('/{conversation}/typing', [ConversationController::class, 'typing'])->name('typing');
            Route::post('/{conversation}/archive', [ConversationController::class, 'archive'])->name('archive');

            // Message Routes
            Route::get('/{conversation}/messages', [MessageController::class, 'index'])->name('messages.index');
            Route::post('/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
            Route::get('/{conversation}/messages/more', [MessageController::class, 'loadMore'])->name('messages.more');
            Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
            Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
            Route::post('/{conversation}/messages/read', [MessageController::class, 'markAsRead'])->name('messages.read');
            Route::get('/search', [MessageController::class, 'search'])->name('search');
        });

        Route::middleware('permission:view_audit_logs')->group(function () {
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
            Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
        });

        // Inquiries Management
        Route::middleware('permission:view_inquiries|manage_inquiries')->group(function () {
            Route::get('/inquiries', [\App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('inquiries.index');
            Route::get('/inquiries/create', [\App\Http\Controllers\Admin\InquiryController::class, 'create'])->name('inquiries.create');
            Route::post('/inquiries', [\App\Http\Controllers\Admin\InquiryController::class, 'store'])->name('inquiries.store');
            Route::get('/inquiries/{inquiry}', [\App\Http\Controllers\Admin\InquiryController::class, 'show'])->name('inquiries.show');
        });

        Route::middleware('permission:manage_inquiries')->group(function () {
            Route::post('/inquiries/{inquiry}/assign', [\App\Http\Controllers\Admin\InquiryController::class, 'assign'])->name('inquiries.assign');
            Route::post('/inquiries/{inquiry}/status', [\App\Http\Controllers\Admin\InquiryController::class, 'updateStatus'])->name('inquiries.status');
            Route::post('/inquiries/{inquiry}/reply', [\App\Http\Controllers\Admin\InquiryController::class, 'reply'])->name('inquiries.reply');
            Route::delete('/inquiries/{inquiry}', [\App\Http\Controllers\Admin\InquiryController::class, 'destroy'])->name('inquiries.destroy');
        });

        // FAQ Management
        Route::middleware('permission:manage_faqs')->group(function () {
            Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index');
            Route::get('/faqs/create', [FaqController::class, 'create'])->name('faqs.create');
            Route::post('/faqs', [FaqController::class, 'store'])->name('faqs.store');
            Route::get('/faqs/{faq}', [FaqController::class, 'edit'])->name('faqs.edit');
            Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update');
            Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('faqs.destroy');
            Route::post('/faqs/{faq}/toggle', [FaqController::class, 'toggle'])->name('faqs.toggle');
            Route::post('/faqs/reorder', [FaqController::class, 'reorder'])->name('faqs.reorder');
        });

        // Intake Management (admin, registrar only)
        Route::middleware('permission:manage_intakes')->group(function () {
            Route::get('/intakes', [IntakeController::class, 'index'])->name('intakes.index');
            Route::get('/intakes/create', [IntakeController::class, 'create'])->name('intakes.create');
            Route::post('/intakes', [IntakeController::class, 'store'])->name('intakes.store');
            Route::get('/intakes/{intake}/edit', [IntakeController::class, 'edit'])->name('intakes.edit');
            Route::put('/intakes/{intake}', [IntakeController::class, 'update'])->name('intakes.update');
            Route::delete('/intakes/{intake}', [IntakeController::class, 'destroy'])->name('intakes.destroy');
            Route::post('/intakes/{intake}/set-current', [IntakeController::class, 'setCurrent'])->name('intakes.set-current');
        });

        // Admission Letters (admin, registrar only)
        Route::middleware('permission:view_admission_letters|create_admission_letter')->group(function () {
            Route::get('/admission-letters', [AdmissionLetterController::class, 'index'])->name('admission-letters.index');
            Route::get('/admission-letters/create', [AdmissionLetterController::class, 'create'])->name('admission-letters.create');
            Route::post('/admission-letters', [AdmissionLetterController::class, 'store'])->name('admission-letters.store');
            Route::get('/admission-letters/bulk', [AdmissionLetterController::class, 'bulkCreate'])->name('admission-letters.bulk-create');
            Route::post('/admission-letters/bulk', [AdmissionLetterController::class, 'bulkStore'])->name('admission-letters.bulk-store');
        });

        Route::middleware('permission:view_admission_letters')->group(function () {
            Route::get('/admission-letters/{letter}', [AdmissionLetterController::class, 'show'])->name('admission-letters.show');
            Route::get('/admission-letters/{letter}/download', [AdmissionLetterController::class, 'download'])->name('admission-letters.download');
        });

        Route::middleware('permission:send_admission_letter')->group(function () {
            Route::post('/admission-letters/{letter}/resend', [AdmissionLetterController::class, 'resend'])->name('admission-letters.resend');
        });

        // Letter Templates
        Route::middleware('permission:manage_settings')->group(function () {
            Route::get('/letter-templates', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'index'])->name('letter-templates.index');
            Route::get('/letter-templates/create', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'create'])->name('letter-templates.create');
            Route::post('/letter-templates', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'store'])->name('letter-templates.store');
            Route::get('/letter-templates/{letterTemplate}/edit', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'edit'])->name('letter-templates.edit');
            Route::put('/letter-templates/{letterTemplate}', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'update'])->name('letter-templates.update');
            Route::delete('/letter-templates/{letterTemplate}', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'destroy'])->name('letter-templates.destroy');
            Route::get('/letter-templates/{letterTemplate}/history', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'history'])->name('letter-templates.history');
            Route::post('/letter-templates/{letterTemplate}/revert/{version}', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'revert'])->name('letter-templates.revert');
            Route::post('/letter-templates/{letterTemplate}/activate', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'activate'])->name('letter-templates.activate');
            Route::get('/letter-templates/preview', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'preview'])->name('letter-templates.preview');
            Route::get('/letter-templates/generate', [\App\Http\Controllers\Admin\LetterTemplateController::class, 'generate'])->name('letter-templates.generate');
        });

        // Document Verification
        Route::middleware('permission:view_documents|verify_documents')->group(function () {
            Route::get('/documents', [DocumentVerificationController::class, 'index'])->name('documents.index');
        });

        Route::middleware('permission:verify_documents')->group(function () {
            Route::post('/documents/{document}/verify', [DocumentVerificationController::class, 'verify'])->name('documents.verify');
            Route::post('/documents/{document}/reject', [DocumentVerificationController::class, 'reject'])->name('documents.reject');
            Route::post('/documents/bulk-verify', [DocumentVerificationController::class, 'bulkVerify'])->name('documents.bulk-verify');
        });

        // Settings (admin only)
        Route::middleware('permission:view_settings')->group(function () {
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
            Route::post('/settings/{group}', [SettingsController::class, 'update'])->name('settings.update');
        });

        // AI Settings (admin only)
        Route::middleware('permission:view_settings')->group(function () {
            Route::get('/ai-settings', [\App\Http\Controllers\Admin\AISettingsController::class, 'index'])->name('ai-settings.index');
            Route::post('/ai-settings', [\App\Http\Controllers\Admin\AISettingsController::class, 'update'])->name('ai-settings.update');
            Route::post('/ai-settings/embed-code', [\App\Http\Controllers\Admin\AISettingsController::class, 'generateEmbedCode'])->name('ai-settings.embed-code');
            Route::post('/ai-settings/regenerate-key', [\App\Http\Controllers\Admin\AISettingsController::class, 'regenerateApiKey'])->name('ai-settings.regenerate-key');
            Route::post('/ai-settings/knowledge', [\App\Http\Controllers\Admin\AISettingsController::class, 'storeKnowledge'])->name('ai-settings.knowledge.store');
            Route::delete('/ai-settings/knowledge/{knowledge}', [\App\Http\Controllers\Admin\AISettingsController::class, 'destroyKnowledge'])->name('ai-settings.knowledge.destroy');
            Route::post('/ai-settings/crawl', [\App\Http\Controllers\Admin\AISettingsController::class, 'crawl'])->name('ai-settings.crawl');
            Route::get('/ai-settings/crawl/{crawlJob}/status', [\App\Http\Controllers\Admin\AISettingsController::class, 'crawlStatus'])->name('ai-settings.crawl.status');
        });

        // AI Conversations (admin only)
        Route::middleware('permission:ai.view_conversations')->group(function () {
            Route::get('/ai-conversations', [\App\Http\Controllers\Admin\AIConversationsController::class, 'index'])->name('ai.conversations.index');
            Route::get('/ai-conversations/escalated', [\App\Http\Controllers\Admin\AIConversationsController::class, 'escalated'])->name('ai.conversations.escalated');
            Route::get('/ai-conversations/{conversation}', [\App\Http\Controllers\Admin\AIConversationsController::class, 'show'])->name('ai.conversations.show');
            Route::post('/ai-conversations/{conversation}', [\App\Http\Controllers\Admin\AIConversationsController::class, 'update'])->name('ai.conversations.update');
            Route::post('/ai-conversations/{conversation}/resolve', [\App\Http\Controllers\Admin\AIConversationsController::class, 'resolve'])->name('ai.conversations.resolve');
            Route::delete('/ai-conversations/{conversation}', [\App\Http\Controllers\Admin\AIConversationsController::class, 'destroy'])->name('ai.conversations.destroy');
        });

        // AI Leads (admin only)
        Route::middleware('permission:ai.view_leads')->group(function () {
            Route::get('/ai-leads', [\App\Http\Controllers\Admin\AILeadsController::class, 'index'])->name('ai.leads.index');
            Route::get('/ai-leads/{lead}', [\App\Http\Controllers\Admin\AILeadsController::class, 'show'])->name('ai.leads.show');
            Route::post('/ai-leads/{lead}', [\App\Http\Controllers\Admin\AILeadsController::class, 'update'])->name('ai.leads.update');
            Route::delete('/ai-leads/{lead}', [\App\Http\Controllers\Admin\AILeadsController::class, 'destroy'])->name('ai.leads.destroy');
            Route::get('/ai-leads-export', [\App\Http\Controllers\Admin\AILeadsController::class, 'export'])->name('ai.leads.export');
        });

        // Notification Templates (admin only)
        Route::middleware('permission:manage_notification_templates')->group(function () {
            Route::get('/notification-templates', [\App\Http\Controllers\Admin\NotificationTemplateController::class, 'index'])->name('notification-templates.index');
            Route::get('/notification-templates/{template}/edit', [\App\Http\Controllers\Admin\NotificationTemplateController::class, 'edit'])->name('notification-templates.edit');
            Route::put('/notification-templates/{template}', [\App\Http\Controllers\Admin\NotificationTemplateController::class, 'update'])->name('notification-templates.update');
            Route::post('/notification-templates/{template}/reset', [\App\Http\Controllers\Admin\NotificationTemplateController::class, 'reset'])->name('notification-templates.reset');
            Route::post('/notification-templates/reset-all', [\App\Http\Controllers\Admin\NotificationTemplateController::class, 'resetAll'])->name('notification-templates.reset-all');
        });

        Route::middleware('permission:view_roles|create_role|edit_role|delete_role')->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::middleware('permission:create_role')->group(function () {
                Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
                Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            });
            Route::middleware('permission:edit_role')->group(function () {
                Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
                Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            });
            Route::middleware('permission:delete_role')->group(function () {
                Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
            });
            Route::get('/roles/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');

            Route::get('/roles/assign', [RoleAssignmentController::class, 'index'])->name('roles.assign.index');
            Route::post('/roles/assign/{user}', [RoleAssignmentController::class, 'assign'])->name('roles.assign');
            Route::delete('/roles/assign/{user}', [RoleAssignmentController::class, 'revoke'])->name('roles.revoke');
            Route::post('/roles/assign-bulk', [RoleAssignmentController::class, 'bulkAssign'])->name('roles.assign.bulk');
        });

        // Subscription Management
        Route::middleware('role:admin')->group(function () {
            Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
            Route::get('/subscription/show', [SubscriptionController::class, 'show'])->name('subscription.show');
            Route::get('/subscription/choose-plan', [SubscriptionController::class, 'choosePlan'])->name('subscription.choose-plan');
            Route::post('/subscription/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
            Route::post('/subscription/renew/{subscription}', [SubscriptionController::class, 'renew'])->name('subscription.renew');
            Route::post('/subscription/cancel/{subscription}', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
            Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
            Route::get('/subscription/features', [SubscriptionController::class, 'features'])->name('subscription.features');
        });
    });

    Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

        // Profile Management
        Route::get('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::get('/profile/notifications', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'notifications'])->name('profile.notifications');
        Route::post('/profile/notifications/{id}/read', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'markNotificationRead'])->name('profile.notifications.read');
        Route::post('/profile/notifications/read-all', [\App\Http\Controllers\SuperAdmin\ProfileController::class, 'markAllNotificationsRead'])->name('profile.notifications.read-all');

        // School Management
        Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
        Route::get('/schools/create', [SchoolController::class, 'create'])->name('schools.create');
        Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
        Route::get('/schools/{school}', [SchoolController::class, 'show'])->name('schools.show');
        Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
        Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
        Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');
        Route::post('/schools/{school}/set-current', [SchoolController::class, 'setCurrent'])->name('schools.set-current');
        Route::post('/schools/stop-impersonating', [SchoolController::class, 'stopImpersonating'])->name('schools.stop-impersonating');
        Route::post('/schools/{school}/admin', [SchoolController::class, 'createAdmin'])->name('schools.admin.create');
        Route::get('/schools/{school}/settings', [SchoolController::class, 'settings'])->name('schools.settings');
        Route::post('/schools/{school}/settings', [SchoolController::class, 'updateSettings'])->name('schools.settings.update');

        // School Subscription Management
        Route::get('/schools/{school}/subscription', [SchoolController::class, 'subscription'])->name('schools.subscription');
        Route::post('/schools/{school}/subscription', [SchoolController::class, 'createSubscription'])->name('schools.subscription.store');
        Route::post('/schools/{school}/extend-trial', [SchoolController::class, 'extendTrial'])->name('schools.extend-trial');
        Route::post('/schools/{school}/activate', [SchoolController::class, 'activateSubscription'])->name('schools.activate');
        Route::post('/schools/{school}/suspend', [SchoolController::class, 'suspendSchool'])->name('schools.suspend');
        Route::post('/schools/{school}/reactivate', [SchoolController::class, 'reactivateSchool'])->name('schools.reactivate');
        Route::post('/schools/{school}/update-expiry', [SchoolController::class, 'updateExpiry'])->name('schools.update-expiry');

        // Subscription Management
        Route::get('/subscriptions', [SuperAdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{subscription}', [SuperAdminSubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::post('/subscriptions/{subscription}/extend', [SuperAdminSubscriptionController::class, 'extend'])->name('subscriptions.extend');
        Route::post('/subscriptions/{subscription}/cancel', [SuperAdminSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
        Route::post('/subscriptions/{subscription}/reactivate', [SuperAdminSubscriptionController::class, 'reactivate'])->name('subscriptions.reactivate');

        // Plan Management
        Route::get('/plans', [SuperAdminSubscriptionController::class, 'plans'])->name('plans.index');
        Route::get('/plans/create', [SuperAdminSubscriptionController::class, 'createPlan'])->name('plans.create');
        Route::post('/plans', [SuperAdminSubscriptionController::class, 'storePlan'])->name('plans.store');
        Route::get('/plans/{plan}/edit', [SuperAdminSubscriptionController::class, 'editPlan'])->name('plans.edit');
        Route::put('/plans/{plan}', [SuperAdminSubscriptionController::class, 'updatePlan'])->name('plans.update');
        Route::delete('/plans/{plan}', [SuperAdminSubscriptionController::class, 'deletePlan'])->name('plans.destroy');

        // Global User Management
        Route::get('/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\SuperAdmin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\SuperAdmin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\SuperAdmin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\SuperAdmin\UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/{user}/generate-password', [\App\Http\Controllers\SuperAdmin\UserController::class, 'generateAndSendPassword'])->name('users.generate-password');
        Route::post('/users/{user}/impersonate', [\App\Http\Controllers\SuperAdmin\UserController::class, 'impersonate'])->name('users.impersonate');
        Route::post('/stop-impersonating', [\App\Http\Controllers\SuperAdmin\UserController::class, 'stopImpersonating'])->name('users.stop-impersonating');

        // Activity Logs
        Route::get('/activity-logs', [\App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'index'])->name('activity-logs');
        Route::get('/activity-logs/export', [\App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('/activity-logs/statistics', [\App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'statistics'])->name('activity-logs.statistics');

        // System Health
        Route::get('/system-health', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'systemHealth'])->name('system-health');

        // Broadcast Notifications
        Route::middleware('permission:view_broadcast|create_broadcast')->group(function () {
            Route::match(['get', 'post'], '/broadcast', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'index'])->name('broadcast');
            Route::get('/broadcast/create', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'create'])->name('broadcast.create');
            Route::post('/broadcast', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'store'])->name('broadcast.store');
            Route::get('/broadcast/{broadcast}', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'show'])->name('broadcast.show');
            Route::get('/broadcast/{broadcast}/preview', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'preview'])->name('broadcast.preview');
            Route::get('/broadcast/stats', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'stats'])->name('broadcast.stats');
        });

        Route::middleware('permission:delete_broadcast')->group(function () {
            Route::delete('/broadcast/{broadcast}', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'destroy'])->name('broadcast.destroy');
        });

        Route::middleware('permission:edit_broadcast|cancel_broadcast')->group(function () {
            Route::post('/broadcast/{broadcast}/cancel', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'cancel'])->name('broadcast.cancel');
        });

        Route::middleware('permission:create_broadcast')->group(function () {
            Route::post('/broadcast/{broadcast}/resend', [\App\Http\Controllers\SuperAdmin\BroadcastController::class, 'resend'])->name('broadcast.resend');
        });

        // Super Admin Internal Messaging
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [ConversationController::class, 'index'])->name('index');
            Route::get('/create', [ConversationController::class, 'create'])->name('create');
            Route::post('/', [ConversationController::class, 'store'])->name('store');
            Route::get('/users', [ConversationController::class, 'getUsers'])->name('users');
            Route::post('/start', [ConversationController::class, 'startConversation'])->name('start');
            Route::get('/unread-count', [ConversationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/{conversation}', [ConversationController::class, 'show'])->name('show');
            Route::post('/{conversation}/read', [ConversationController::class, 'markAsRead'])->name('read');
            Route::post('/{conversation}/typing', [ConversationController::class, 'typing'])->name('typing');
            Route::get('/{conversation}/messages', [MessageController::class, 'index'])->name('messages.index');
            Route::post('/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
            Route::get('/{conversation}/messages/more', [MessageController::class, 'loadMore'])->name('messages.more');
            Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
            Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
            Route::post('/{conversation}/messages/read', [MessageController::class, 'markAsRead'])->name('messages.read');
        });

        // Reports & Exports
        Route::get('/reports/export/{type}', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'exportReport'])->name('reports.export');

        // Global Settings
        Route::get('/settings', [GlobalSettingsController::class, 'index'])->name('settings');
        Route::post('/settings/{group}', [GlobalSettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/clear-cache', [GlobalSettingsController::class, 'clearCache'])->name('settings.clear-cache');

        // AI Settings
        Route::get('/ai-settings', [AISettingsController::class, 'index'])->name('ai-settings.index');
        Route::post('/ai-settings', [AISettingsController::class, 'update'])->name('ai-settings.update');
        Route::get('/ai-settings/knowledge', [AISettingsController::class, 'knowledge'])->name('ai-settings.knowledge');
        Route::post('/ai-settings/knowledge', [AISettingsController::class, 'storeKnowledge'])->name('ai-settings.knowledge.store');
        Route::get('/ai-settings/knowledge/{knowledge}', [AISettingsController::class, 'getKnowledge'])->name('ai-settings.knowledge.show');
        Route::put('/ai-settings/knowledge/{knowledge}', [AISettingsController::class, 'updateKnowledge'])->name('ai-settings.knowledge.update');
        Route::delete('/ai-settings/knowledge/{knowledge}', [AISettingsController::class, 'destroyKnowledge'])->name('ai-settings.knowledge.destroy');
        Route::get('/ai-settings/analytics', [AISettingsController::class, 'analytics'])->name('ai-settings.analytics');

        // Backup Management
        Route::get('/backups', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/full', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'createFullBackup'])->name('backups.create-full');
        Route::post('/backups/database', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'createDatabaseBackup'])->name('backups.create-database');
        Route::post('/backups/upload', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'upload'])->name('backups.upload');
        Route::post('/backups/restore-upload', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'restoreUpload'])->name('backups.restore-upload');
        Route::get('/backups/{backup}/download', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'download'])->name('backups.download');
        Route::post('/backups/{backup}/restore', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('/backups/{backup}', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('/backups/cleanup', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'cleanup'])->name('backups.cleanup');

        // Global Program Management
        Route::get('/programs', [GlobalProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/create', [GlobalProgramController::class, 'create'])->name('programs.create');
        Route::post('/programs', [GlobalProgramController::class, 'store'])->name('programs.store');
        Route::get('/programs/{program}', [GlobalProgramController::class, 'show'])->name('programs.show');
        Route::get('/programs/{program}/edit', [GlobalProgramController::class, 'edit'])->name('programs.edit');
        Route::put('/programs/{program}', [GlobalProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{program}', [GlobalProgramController::class, 'destroy'])->name('programs.destroy');
        Route::post('/programs/{program}/toggle-status', [GlobalProgramController::class, 'toggleStatus'])->name('programs.toggle-status');
        Route::get('/programs/get-departments', [GlobalProgramController::class, 'getDepartments'])->name('programs.get-departments');

        // Application Oversight
        Route::get('/applications', [ApplicationsController::class, 'index'])->name('applications.index');
        Route::get('/applications/export', [ApplicationsController::class, 'export'])->name('applications.export');
        Route::get('/applications/{application}', [ApplicationsController::class, 'show'])->name('applications.show');
        Route::get('/applications/school/{school}', [ApplicationsController::class, 'showBySchool'])->name('applications.by-school');
        Route::post('/applications/bulk-action', [ApplicationsController::class, 'bulkAction'])->name('applications.bulk-action');
        Route::get('/applications/get-programs', [ApplicationsController::class, 'getProgramsBySchool'])->name('applications.get-programs');
        Route::get('/applications/get-intakes', [ApplicationsController::class, 'getIntakesBySchool'])->name('applications.get-intakes');

        // Individual Application Actions
        Route::post('/applications/{application}/approve', [ApplicationsController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject', [ApplicationsController::class, 'reject'])->name('applications.reject');

        // Reports & Analytics
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/applications', [ReportsController::class, 'applications'])->name('reports.applications');
        Route::get('/reports/schools', [ReportsController::class, 'schools'])->name('reports.schools');
        Route::get('/reports/programs', [ReportsController::class, 'programs'])->name('reports.programs');
        Route::get('/reports/payments', [ReportsController::class, 'payments'])->name('reports.payments');
        Route::get('/reports/users', [ReportsController::class, 'users'])->name('reports.users');
        Route::get('/reports/analytics', [ReportsController::class, 'analytics'])->name('reports.analytics');
        Route::get('/reports/export/{type}', [ReportsController::class, 'export'])->name('reports.export');

        // Form Fields Management (Global - per school)
        Route::get('/form-fields', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'index'])->name('form-fields.index');
        Route::get('/form-fields/create-section', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'createSection'])->name('form-fields.create-section');
        Route::post('/form-fields/sections', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'storeSection'])->name('form-fields.store-section');
        Route::get('/form-fields/sections/{section}/edit', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'editSection'])->name('form-fields.edit-section');
        Route::put('/form-fields/sections/{section}', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'updateSection'])->name('form-fields.update-section');
        Route::delete('/form-fields/sections/{section}', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'destroySection'])->name('form-fields.destroy-section');
        Route::post('/form-fields/sections/{section}/toggle', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'toggleSection'])->name('form-fields.toggle-section');
        Route::get('/form-fields/sections/{section}/create-field', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'createField'])->name('form-fields.create-field');
        Route::post('/form-fields/sections/{section}/fields', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'storeField'])->name('form-fields.store-field');
        Route::get('/form-fields/fields/{field}/edit', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'editField'])->name('form-fields.edit-field');
        Route::put('/form-fields/fields/{field}', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'updateField'])->name('form-fields.update-field');
        Route::delete('/form-fields/fields/{field}', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'destroyField'])->name('form-fields.destroy-field');
        Route::post('/form-fields/fields/{field}/toggle', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'toggleField'])->name('form-fields.toggle-field');
        Route::post('/form-fields/reset/{school}', [\App\Http\Controllers\SuperAdmin\FormFieldController::class, 'resetToDefaults'])->name('form-fields.reset');

        // FAQ Management
        Route::get('/faqs', [SuperAdminFaqController::class, 'index'])->name('faqs.index');
        Route::get('/faqs/create', [SuperAdminFaqController::class, 'create'])->name('faqs.create');
        Route::post('/faqs', [SuperAdminFaqController::class, 'store'])->name('faqs.store');
        Route::get('/faqs/{faq}', [SuperAdminFaqController::class, 'edit'])->name('faqs.edit');
        Route::put('/faqs/{faq}', [SuperAdminFaqController::class, 'update'])->name('faqs.update');
        Route::delete('/faqs/{faq}', [SuperAdminFaqController::class, 'destroy'])->name('faqs.destroy');
        Route::post('/faqs/{faq}/toggle', [SuperAdminFaqController::class, 'toggle'])->name('faqs.toggle');

        // Roles & Permissions Management
        Route::get('/roles', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/permissions', [\App\Http\Controllers\SuperAdmin\RoleController::class, 'permissions'])->name('roles.permissions');
    });

    Route::fallback(function () {
        return redirect()->route('home');
    });
});

// Public letter verification route
Route::get('/letter/verify/{letterNumber}', function ($letterNumber) {
    $letter = \App\Models\AdmissionLetter::where('letter_number', $letterNumber)
        ->with(['application.student', 'application.program', 'school'])
        ->first();

    if (!$letter) {
        return response()->json(['valid' => false, 'message' => 'Letter not found'], 404);
    }

    return response()->json([
        'valid' => true,
        'letter' => [
            'number' => $letter->letter_number,
            'type' => $letter->type,
            'status' => $letter->status,
            'issue_date' => $letter->issue_date?->toDateString(),
            'student' => $letter->application->student?->fullName(),
            'program' => $letter->application->program?->name,
            'school' => $letter->school?->name,
        ]
    ]);
})->name('letter.verify');
