<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MfaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\School\OnboardingController;
use App\Http\Controllers\School\SettingsController;
use App\Http\Controllers\School\CampusController;
use App\Http\Controllers\School\StaffController;
use App\Http\Controllers\School\ChecklistController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\LandingPageController;
use App\Http\Controllers\School\StudentController;
use App\Http\Controllers\School\StaffHrController;
use App\Http\Controllers\School\AcademicStructureController;
use App\Http\Controllers\School\SubjectController;
use App\Http\Controllers\School\AdmissionsController;
use App\Http\Controllers\School\TimetableController;
use App\Http\Controllers\School\AttendanceController;
use App\Http\Controllers\School\ScoringConfigurationController;
use App\Http\Controllers\School\ScoreEntryController;
use App\Http\Controllers\School\ReportCardController;
use App\Http\Controllers\School\FeeStructureController;
use App\Http\Controllers\School\InvoiceController;
use App\Http\Controllers\School\PaymentController;
use App\Http\Controllers\School\AccountingController;
use App\Http\Controllers\School\WebsiteBuilderController;
use App\Http\Controllers\PublicWebsiteController;
use App\Http\Controllers\School\StudentPortalController;
use App\Http\Controllers\School\ParentPortalController;
use App\Http\Controllers\School\LmsController;
use App\Http\Controllers\School\OperationsController;
use App\Http\Controllers\School\CommunicationController;
use App\Http\Controllers\School\AiController;
use App\Http\Controllers\School\SearchController;
use App\Http\Controllers\School\ApiKeyController;
use App\Http\Controllers\School\BillingController;

Route::get('/', function () {
    return view('welcome');
});

// Dynamic XML Sitemap Route for SEO crawlers
Route::get('/sitemap.xml', function () {
    $now = now()->toAtomString();
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
    // Homepage
    $xml .= '<url>';
    $xml .= '<loc>' . url('/') . '</loc>';
    $xml .= '<lastmod>' . $now . '</lastmod>';
    $xml .= '<changefreq>daily</changefreq>';
    $xml .= '<priority>1.0</priority>';
    $xml .= '</url>';
    
    // Login page
    $xml .= '<url>';
    $xml .= '<loc>' . url('/login') . '</loc>';
    $xml .= '<lastmod>' . $now . '</lastmod>';
    $xml .= '<changefreq>monthly</changefreq>';
    $xml .= '<priority>0.8</priority>';
    $xml .= '</url>';
    
    // Register page
    $xml .= '<url>';
    $xml .= '<loc>' . url('/register') . '</loc>';
    $xml .= '<lastmod>' . $now . '</lastmod>';
    $xml .= '<changefreq>monthly</changefreq>';
    $xml .= '<priority>0.8</priority>';
    $xml .= '</url>';
    
    // Add active tenant school websites automatically
    try {
        $schools = \App\Models\School::where('is_active', true)->get();
        foreach ($schools as $school) {
            $domain = $school->domain ?: ($school->subdomain . '.' . parse_url(config('app.url'), PHP_URL_HOST));
            $xml .= '<url>';
            $xml .= '<loc>https://' . $domain . '</loc>';
            $xml .= '<lastmod>' . $now . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.7</priority>';
            $xml .= '</url>';
        }
    } catch (\Exception $e) {
        // Fallback silently if DB not ready
    }
    
    $xml .= '</urlset>';
    
    return response($xml, 200, ['Content-Type' => 'application/xml']);
});

// Public Admissions Application Route
Route::get('/admissions/apply', [AdmissionsController::class, 'publicForm'])->name('school.admissions.apply');
Route::post('/admissions/apply', [AdmissionsController::class, 'submitForm'])->name('school.admissions.submit');

// Authentication Routes (School users)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');

// Registration Routes (School guest users)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Platform Admin Authentication Routes
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'adminLogin']);

// Multi-Factor Authentication Routes (MFA OTP)
Route::get('/login/mfa', [MfaController::class, 'showMfaForm'])->name('login.mfa');
Route::post('/login/mfa', [MfaController::class, 'verifyMfa'])->name('login.mfa.verify');

// Super Admin Group (Protected by Auth and Role check)
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'index'])->name('analytics');
    Route::post('/billing/override/{schoolId}', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'updateTenantSubscription'])->name('billing.override');
    Route::post('/billing/sms', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'updateSmsCredits'])->name('billing.sms');
    Route::post('/settings/update', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'updateSystemSettings'])->name('settings.update');
    Route::post('/settings/sms-test', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'sendTestSms'])->name('settings.sms-test');

    // Dedicated Super Admin Pages
    Route::get('/sms-credits', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'smsCreditsIndex'])->name('sms-credits');
    Route::get('/access-logs', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'accessLogsIndex'])->name('access-logs');
    Route::get('/settings', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'settingsIndex'])->name('settings');
    Route::get('/env-settings', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'envSettingsIndex'])->name('env-settings');
    Route::post('/env-settings/update', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'updateEnvSettings'])->name('env-settings.update');
    Route::post('/env-settings/restore', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'restoreEnvSettings'])->name('env-settings.restore');
    Route::post('/schools/{schoolId}/approve', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'approveSchool'])->name('schools.approve');
    Route::get('/email-settings', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'emailSettingsIndex'])->name('email-settings');
    Route::post('/email-settings/update', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'updateEmailSettings'])->name('email-settings.update');
    Route::post('/email-settings/send', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'sendCustomEmail'])->name('email-settings.send');
    Route::post('/email-settings/test', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'testEmailSettings'])->name('email-settings.test');
    Route::delete('/email-settings/logs/{id}', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'destroyEmailLog'])->name('email-settings.logs.destroy');
    Route::post('/users/{userId}/toggle-status', [DashboardController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/schools/{schoolId}/toggle-status', [DashboardController::class, 'toggleSchoolStatus'])->name('schools.toggle-status');
    Route::post('/schools/{schoolId}/impersonate', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'impersonateSchool'])->name('schools.impersonate');
    Route::resource('/roles', App\Http\Controllers\SuperAdmin\SuperAdminRoleController::class)->names('roles');
    Route::resource('/plans', App\Http\Controllers\SuperAdmin\SuperAdminPlanController::class)->names('plans');
    Route::resource('/documentation', App\Http\Controllers\SuperAdmin\SuperAdminDocumentationController::class)->names('documentation');

    // SaaS Landing Welcome Page Editor
    Route::get('/landing-page', [LandingPageController::class, 'edit'])->name('landing-page.edit');
    Route::post('/landing-page/update', [LandingPageController::class, 'update'])->name('landing-page.update');

    // Help Center Configurations
    Route::get('/help-settings', [App\Http\Controllers\SuperAdmin\SuperAdminHelpSettingsController::class, 'index'])->name('help-settings.index');
    Route::post('/help-settings/update', [App\Http\Controllers\SuperAdmin\SuperAdminHelpSettingsController::class, 'update'])->name('help-settings.update');
});

// School/Tenant Onboarding and Settings Routes
Route::middleware(['auth', 'tenant'])->prefix('{school_subdomain}')->group(function () {
    Route::post('/impersonate/stop', [App\Http\Controllers\SuperAdmin\SuperAdminAnalyticsController::class, 'stopImpersonating'])->name('super-admin.schools.impersonate.stop');
    
    Route::get('/help-center', [App\Http\Controllers\School\HelpCenterController::class, 'index'])->name('school.help-center.index');
    Route::get('/help-center/{slug}', [App\Http\Controllers\School\HelpCenterController::class, 'show'])->name('school.help-center.show');

    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('profile.password');

    Route::get('/school/onboarding', [OnboardingController::class, 'index'])->name('school.onboarding');
    Route::post('/school/onboarding', [OnboardingController::class, 'store'])->name('school.onboarding.store');
    
    // Onboarding Checklist
    Route::get('/school/checklist', [ChecklistController::class, 'index'])->name('school.checklist');

    // General, Gateway, and Feature Settings (Protected)
    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::get('/school/settings', [SettingsController::class, 'index'])->name('school.settings');
        Route::post('/school/settings/profile', [SettingsController::class, 'updateProfile'])->name('school.settings.profile');
        Route::post('/school/settings/gateway', [SettingsController::class, 'updateGateway'])->name('school.settings.gateway');
        Route::post('/school/settings/features', [SettingsController::class, 'updateFeatures'])->name('school.settings.features');
        Route::post('/school/settings/payments', [SettingsController::class, 'updatePaymentSettings'])->name('school.settings.payments');
        Route::post('/school/settings/grading-scales/{scale}', [SettingsController::class, 'updateGradingScale'])->name('school.settings.grading-scale.update');

        // Promotion Rules Setup Routes
        Route::get('/school/settings/promotions', [\App\Http\Controllers\School\PromotionRuleController::class, 'index'])->name('school.settings.promotions.index');
        Route::post('/school/settings/promotions', [\App\Http\Controllers\School\PromotionRuleController::class, 'store'])->name('school.settings.promotions.store');
        Route::put('/school/settings/promotions/{id}', [\App\Http\Controllers\School\PromotionRuleController::class, 'update'])->name('school.settings.promotions.update');
        Route::delete('/school/settings/promotions/{id}', [\App\Http\Controllers\School\PromotionRuleController::class, 'destroy'])->name('school.settings.promotions.destroy');

        // API Keys Management
        Route::get('/school/api-keys', [ApiKeyController::class, 'index'])->name('school.api-keys.index');
        Route::post('/school/api-keys', [ApiKeyController::class, 'store'])->name('school.api-keys.store');
        Route::delete('/school/api-keys/{id}', [ApiKeyController::class, 'destroy'])->name('school.api-keys.destroy');

        // SaaS Billing Management
        Route::get('/school/billing', [BillingController::class, 'index'])->name('school.billing.index');
        Route::post('/school/billing/checkout', [BillingController::class, 'checkout'])->name('school.billing.checkout');
        Route::post('/school/billing/unlock-website', [BillingController::class, 'unlockWebsite'])->name('school.billing.unlock-website');
        Route::get('/school/billing/gateway', [BillingController::class, 'gatewayPayment'])->name('school.billing.gateway');
        Route::post('/school/billing/process-payment', [BillingController::class, 'processPayment'])->name('school.billing.process-payment');
    });

    // Campus Management CRUD (Protected)
    Route::middleware(['permission:manage-campuses'])->group(function () {
        Route::get('/school/campuses', [CampusController::class, 'index'])->name('school.campuses');
        Route::post('/school/campuses', [CampusController::class, 'store'])->name('school.campuses.store');
        Route::put('/school/campuses/{campus}', [CampusController::class, 'update'])->name('school.campuses.update');
        Route::delete('/school/campuses/{campus}', [CampusController::class, 'destroy'])->name('school.campuses.destroy');
    });

    // Staff Accounts CRUD (Protected)
    Route::middleware(['permission:manage-staff'])->group(function () {
        Route::get('/school/staff', [StaffController::class, 'index'])->name('school.staff');
        Route::get('/school/staff/print-pdf', [StaffController::class, 'printPdf'])->name('school.staff.print-pdf');
        Route::post('/school/staff', [StaffController::class, 'store'])->name('school.staff.store');
        Route::put('/school/staff/{staff}', [StaffController::class, 'update'])->name('school.staff.update');
        Route::post('/school/staff/{staff}/toggle', [StaffController::class, 'toggleStatus'])->name('school.staff.toggle');
        Route::post('/school/staff/{staff}/report', [StaffController::class, 'report'])->name('school.staff.report');
        Route::delete('/school/staff/{staff}', [StaffController::class, 'destroy'])->name('school.staff.destroy');

        // Staff HR Sub-views
        Route::get('/school/staff-hr/{staff}', [StaffHrController::class, 'show'])->name('school.staff-hr.show');
        Route::put('/school/staff-hr/{staff}', [StaffHrController::class, 'update'])->name('school.staff-hr.update');
        Route::post('/school/staff-hr/{staff}/qualification', [StaffHrController::class, 'addQualification'])->name('school.staff-hr.qualification');
        Route::post('/school/staff-hr/{staff}/upload', [StaffHrController::class, 'uploadDocument'])->name('school.staff-hr.upload');
    });

    // Student Registry and Admissions CRUD (Protected)
    Route::middleware(['permission:manage-enrollments'])->group(function () {
        Route::get('/school/students', [StudentController::class, 'index'])->name('school.students');
        Route::get('/school/students/print-pdf', [StudentController::class, 'printPdf'])->name('school.students.print-pdf');
        Route::post('/school/students', [StudentController::class, 'store'])->name('school.students.store');
        Route::put('/school/students/{student}', [StudentController::class, 'update'])->name('school.students.update');
        Route::delete('/school/students/{student}', [StudentController::class, 'destroy'])->name('school.students.destroy');
        Route::post('/school/students/{student}/reset-password', [StudentController::class, 'resetPortalPassword'])->name('school.students.reset-password');

        // Student Promotions
        Route::get('/school/students/promotion', [\App\Http\Controllers\School\StudentPromotionController::class, 'index'])->name('school.students.promotion');
        Route::post('/school/students/promotion/process', [\App\Http\Controllers\School\StudentPromotionController::class, 'process'])->name('school.students.promotion.process');

        // Admissions CRM
        Route::get('/school/admissions', [AdmissionsController::class, 'index'])->name('school.admissions.index');
        Route::post('/school/admissions/{application}/status', [AdmissionsController::class, 'updateStatus'])->name('school.admissions.updateStatus');
        Route::post('/school/admissions/{application}/approve', [AdmissionsController::class, 'approve'])->name('school.admissions.approve');
    });

    // Academic Structure Config (Protected by module:academics)
    Route::middleware(['module:academics'])->group(function () {
        Route::middleware(['permission:manage-academics'])->group(function () {
            Route::get('/school/academics', [AcademicStructureController::class, 'index'])->name('school.academics');
            Route::post('/school/academics/years', [AcademicStructureController::class, 'storeYear'])->name('school.academics.years.store');
            Route::put('/school/academics/years/{id}', [AcademicStructureController::class, 'updateYear'])->name('school.academics.years.update');
            Route::post('/school/academics/terms', [AcademicStructureController::class, 'storeTerm'])->name('school.academics.terms.store');
            Route::put('/school/academics/terms/{id}', [AcademicStructureController::class, 'updateTerm'])->name('school.academics.terms.update');
            Route::post('/school/academics/departments', [AcademicStructureController::class, 'storeDepartment'])->name('school.academics.departments.store');
            Route::put('/school/academics/departments/{id}', [AcademicStructureController::class, 'updateDepartment'])->name('school.academics.departments.update');
            Route::post('/school/academics/programmes', [AcademicStructureController::class, 'storeProgramme'])->name('school.academics.programmes.store');
            Route::put('/school/academics/programmes/{id}', [AcademicStructureController::class, 'updateProgramme'])->name('school.academics.programmes.update');
            Route::post('/school/academics/classes', [AcademicStructureController::class, 'storeClass'])->name('school.academics.classes.store');
            Route::put('/school/academics/classes/{id}', [AcademicStructureController::class, 'updateClass'])->name('school.academics.classes.update');
            Route::post('/school/academics/streams', [AcademicStructureController::class, 'storeStream'])->name('school.academics.streams.store');
            Route::put('/school/academics/streams/{id}', [AcademicStructureController::class, 'updateStream'])->name('school.academics.streams.update');

            // Subjects Registry
            Route::get('/school/subjects', [SubjectController::class, 'index'])->name('school.subjects');
            Route::post('/school/subjects', [SubjectController::class, 'store'])->name('school.subjects.store');
            Route::put('/school/subjects/{subject}', [SubjectController::class, 'update'])->name('school.subjects.update');
            Route::delete('/school/subjects/{subject}', [SubjectController::class, 'destroy'])->name('school.subjects.destroy');
            Route::post('/school/subjects/allocate', [SubjectController::class, 'allocateTeacher'])->name('school.subjects.allocate');
            Route::put('/school/subjects/allocate/{id}', [SubjectController::class, 'updateAllocation'])->name('school.subjects.allocate.update');
            Route::delete('/school/subjects/allocate/{id}', [SubjectController::class, 'destroyAllocation'])->name('school.subjects.allocate.destroy');

            // Timetable modify actions
            Route::post('/school/timetable', [TimetableController::class, 'store'])->name('school.timetable.store');
            Route::delete('/school/timetable/{timetable}', [TimetableController::class, 'destroy'])->name('school.timetable.destroy');
        });

        // Timetable read index (Shared)
        Route::get('/school/timetable', [TimetableController::class, 'index'])->name('school.timetable');

        // Attendance Manager (Shared/Teachers)
        Route::get('/school/attendance', [AttendanceController::class, 'index'])->name('school.attendance');
        Route::post('/school/attendance', [AttendanceController::class, 'store'])->name('school.attendance.store');
        Route::get('/school/attendance/qr-kiosk', [AttendanceController::class, 'qrKiosk'])->name('school.attendance.qr-kiosk');
        Route::post('/school/attendance/qr-checkin', [AttendanceController::class, 'qrCheckIn'])->name('school.attendance.qr-checkin');
        Route::get('/school/attendance/reports', [AttendanceController::class, 'reports'])->name('school.attendance.reports');

        // Scoring Wizard Configuration (Protected)
        Route::resource('/school/scoring-configs', ScoringConfigurationController::class)
            ->names('school.scoring-configs')
            ->parameters([
                'scoring-configs' => 'scoringConfig',
            ])
            ->middleware('permission:configure-scoring');

        // Score Entry Spreadsheet (Protected)
        Route::middleware(['permission:enter-scores'])->group(function () {
            Route::get('/school/scores/enter', [ScoreEntryController::class, 'enter'])->name('school.scores.enter');
            Route::post('/school/scores/save-draft', [ScoreEntryController::class, 'saveDraft'])->name('school.scores.save-draft');
            Route::post('/school/scores/submit', [ScoreEntryController::class, 'submit'])->name('school.scores.submit');
            Route::get('/school/scores/export', [ScoreEntryController::class, 'exportCsv'])->name('school.scores.export');
            Route::post('/school/scores/import', [ScoreEntryController::class, 'importCsv'])->name('school.scores.import');
        });

        // Verify & Approve Scores (Moderation Protected)
        Route::post('/school/scores/verify', [ScoreEntryController::class, 'verify'])->name('school.scores.verify')->middleware('permission:verify-scores');
        Route::post('/school/scores/approve', [ScoreEntryController::class, 'approve'])->name('school.scores.approve')->middleware('permission:approve-scores');
        Route::post('/school/scores/unlock', [ScoreEntryController::class, 'unlock'])->name('school.scores.unlock')->middleware('permission:approve-scores');

        // Broadsheets & Reports (Academics/Grades Reports)
        Route::get('/school/reports', [ReportCardController::class, 'index'])->name('school.reports.index');
        Route::get('/school/reports/broadsheet', [ReportCardController::class, 'broadsheet'])->name('school.reports.broadsheet')->middleware('permission:approve-scores');
        Route::get('/school/reports/bulk-print', [ReportCardController::class, 'bulkPrint'])->name('school.reports.bulk-print');
        Route::get('/school/reports/card/{student}', [ReportCardController::class, 'generateCard'])->name('school.reports.card');
        Route::post('/school/reports/student/{student}/details', [ReportCardController::class, 'saveReportDetails'])->name('school.reports.details.store');
        Route::get('/school/reports/themes', [\App\Http\Controllers\School\ReportCardThemeController::class, 'index'])->name('school.reports.themes.index');
        Route::post('/school/reports/themes', [\App\Http\Controllers\School\ReportCardThemeController::class, 'update'])->name('school.reports.themes.update');
    });

    // Finance Module Group (Protected by module:finance)
    Route::prefix('school/finance')->name('school.finance.')->middleware(['module:finance'])->group(function () {
        Route::middleware(['permission:manage-fees'])->group(function () {
            Route::resource('fee-structures', FeeStructureController::class);
            Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
            Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
            Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
            Route::post('invoices/bulk', [InvoiceController::class, 'bulkStore'])->name('invoices.bulk-store');
            Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
            Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
        });

        Route::middleware(['permission:collect-payments'])->group(function () {
            Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
            Route::post('payments/{payment}/reverse', [PaymentController::class, 'reverse'])->name('payments.reverse');
        });

        Route::middleware(['permission:view-accounts'])->group(function () {
            Route::get('accounts', [AccountingController::class, 'accountsIndex'])->name('accounts.index');
            Route::post('accounts', [AccountingController::class, 'accountsStore'])->name('accounts.store');
            Route::get('journals', [AccountingController::class, 'journalsIndex'])->name('journals.index');
            Route::post('journals', [AccountingController::class, 'journalsStore'])->name('journals.store');
            Route::get('reports', [AccountingController::class, 'reportsIndex'])->name('reports.index');
        });
    });

    // Website Builder Module Group (Protected by module:website_builder)
    Route::prefix('school/website')->name('school.website.')->middleware(['module:website_builder', 'website.unlocked', 'permission:manage-website'])->group(function () {
        Route::get('pages', [WebsiteBuilderController::class, 'pagesIndex'])->name('pages.index');
        Route::post('pages', [WebsiteBuilderController::class, 'pagesStore'])->name('pages.store');
        Route::delete('pages/{page}', [WebsiteBuilderController::class, 'pagesDestroy'])->name('pages.destroy');
        Route::get('pages/{page}/builder', [WebsiteBuilderController::class, 'builderEdit'])->name('pages.builder');
        Route::post('pages/{page}/save', [WebsiteBuilderController::class, 'builderSave'])->name('pages.save');
        Route::post('pages/{page}/publish', [WebsiteBuilderController::class, 'builderPublish'])->name('pages.publish');
        Route::get('pages/{page}/revisions', [WebsiteBuilderController::class, 'builderRevisions'])->name('pages.revisions');
        Route::post('revisions/{revision}/rollback', [WebsiteBuilderController::class, 'builderRollback'])->name('revisions.rollback');
        Route::get('navigation', [WebsiteBuilderController::class, 'navigationEdit'])->name('navigation.index');
        Route::post('navigation', [WebsiteBuilderController::class, 'navigationUpdate'])->name('navigation.update');
        Route::get('settings', [WebsiteBuilderController::class, 'settingsEdit'])->name('settings.index');
        Route::post('settings', [WebsiteBuilderController::class, 'settingsUpdate'])->name('settings.update');
    });


    Route::middleware(['module:portals'])->group(function () {
        // Student Portal
        Route::prefix('school/student-portal')->name('school.student-portal.')->group(function () {
            Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
            Route::get('/id-card', [StudentPortalController::class, 'idCard'])->name('id-card');
            Route::get('/timetable', [StudentPortalController::class, 'timetable'])->name('timetable');
            Route::get('/assignments', [StudentPortalController::class, 'assignmentsIndex'])->name('assignments.index');
            Route::post('/assignments/{assignment}/submit', [StudentPortalController::class, 'assignmentSubmit'])->name('assignments.submit');
            Route::get('/results', [StudentPortalController::class, 'resultsIndex'])->name('results.index');
            Route::get('/transport', [StudentPortalController::class, 'transport'])->name('transport');
        });

        // Parent Portal
        Route::prefix('school/parent-portal')->name('school.parent-portal.')->group(function () {
            Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
            Route::get('/child/{student}', [ParentPortalController::class, 'selectChild'])->name('select-child');
            Route::get('/attendance', [ParentPortalController::class, 'attendance'])->name('attendance');
            Route::get('/fees', [ParentPortalController::class, 'fees'])->name('fees');
            Route::get('/reports', [ParentPortalController::class, 'reports'])->name('reports');
            Route::get('/messages', [ParentPortalController::class, 'messages'])->name('messages');
            Route::get('/transport', [ParentPortalController::class, 'transport'])->name('transport');
        });
    });

    // LMS Modules (Protected by module:lms)
    Route::prefix('school/lms')->name('school.lms.')->middleware(['module:lms'])->group(function () {
        Route::get('/courses', [LmsController::class, 'coursesIndex'])->name('courses.index');
        Route::post('/courses', [LmsController::class, 'courseStore'])->name('courses.store');
        Route::get('/courses/{course}', [LmsController::class, 'courseShow'])->name('courses.show');
        Route::post('/courses/{course}/lessons', [LmsController::class, 'lessonStore'])->name('lessons.store');
        Route::post('/courses/{course}/quizzes', [LmsController::class, 'quizStore'])->name('quizzes.store');
        Route::post('/quizzes/{quiz}/questions', [LmsController::class, 'questionStore'])->name('quizzes.questions.store');
        Route::get('/lessons/{lesson}', [LmsController::class, 'lessonShow'])->name('lessons.show');
        Route::post('/lessons/{lesson}/complete', [LmsController::class, 'lessonComplete'])->name('lessons.complete');
        Route::get('/quizzes/{quiz}', [LmsController::class, 'quizShow'])->name('quizzes.show');
        Route::post('/quizzes/{quiz}/submit', [LmsController::class, 'quizSubmit'])->name('quizzes.submit');
        Route::post('/forums/{forum}/post', [LmsController::class, 'forumPostStore'])->name('forums.post.store');
    });

    // Operations Center (Hostels, Transport, Library, Inventory, HR, Health & Discipline)
    Route::prefix('school/operations')->name('school.operations.')->group(function () {
        Route::get('/dashboard', [OperationsController::class, 'dashboard'])->name('dashboard');

        // AI Analytics
        Route::get('/ai-dashboard', [AiController::class, 'dashboard'])->name('ai.dashboard');
        Route::post('/ai/run', [AiController::class, 'runAnalytics'])->name('ai.run');
        Route::get('/ai/suggest-comment', [AiController::class, 'suggestComment'])->name('ai.suggest-comment');
        
        // Library
        Route::middleware(['module:library'])->group(function () {
            Route::get('/library', [OperationsController::class, 'libraryIndex'])->name('library.index');
            Route::post('/library/borrow', [OperationsController::class, 'libraryBorrow'])->name('library.borrow');
            Route::post('/library/return/{loan}', [OperationsController::class, 'libraryReturn'])->name('library.return');
        });

        // Inventory
        Route::middleware(['module:inventory'])->group(function () {
            Route::get('/inventory', [OperationsController::class, 'inventoryIndex'])->name('inventory.index');
            Route::post('/inventory/transaction', [OperationsController::class, 'inventoryTransaction'])->name('inventory.transaction');
        });

        // Hostel
        Route::middleware(['module:hostels'])->group(function () {
            Route::get('/hostel', [OperationsController::class, 'hostelIndex'])->name('hostel.index');
            Route::post('/hostel/allocate', [OperationsController::class, 'hostelAllocate'])->name('hostel.allocate');
            Route::post('/hostel/allocate/{allocation}/vacate', [OperationsController::class, 'hostelDeallocate'])->name('hostel.allocate.vacate');
            Route::post('/hostel/dormitory', [OperationsController::class, 'dormitoryStore'])->name('hostel.dormitory.store');
            Route::put('/hostel/dormitory/{dormitory}', [OperationsController::class, 'dormitoryUpdate'])->name('hostel.dormitory.update');
            Route::delete('/hostel/dormitory/{dormitory}', [OperationsController::class, 'dormitoryDestroy'])->name('hostel.dormitory.destroy');
        });

        // Transport
        Route::middleware(['module:transport'])->group(function () {
            Route::get('/transport', [OperationsController::class, 'transportIndex'])->name('transport.index');
            Route::post('/transport/route', [OperationsController::class, 'transportRouteStore'])->name('transport.route.store');
            Route::post('/transport/route/{route}/stop', [OperationsController::class, 'transportStopStore'])->name('transport.stop.store');
            Route::post('/transport/vehicle', [OperationsController::class, 'vehicleStore'])->name('transport.vehicle.store');
            Route::delete('/transport/route/{route}', [OperationsController::class, 'transportRouteDelete'])->name('transport.route.delete');
            Route::delete('/transport/vehicle/{vehicle}', [OperationsController::class, 'vehicleDelete'])->name('transport.vehicle.delete');
        });

        // HR & Payroll (Protected by manage-staff permission)
        Route::middleware(['module:hr_payroll', 'permission:manage-staff'])->group(function () {
            Route::get('/hr', [OperationsController::class, 'hrIndex'])->name('hr.index');
            Route::post('/hr/leave', [OperationsController::class, 'hrLeaveApply'])->name('hr.leave');
            Route::post('/hr/payroll/run', [OperationsController::class, 'hrPayrollRun'])->name('hr.payroll.run');
            Route::get('/hr/payslips/{payslip}', [OperationsController::class, 'hrPayslipShow'])->name('hr.payslip');
        });

        // Health & Discipline
        Route::middleware(['module:health_discipline'])->group(function () {
            Route::get('/health-discipline', [OperationsController::class, 'healthDisciplineIndex'])->name('health-discipline.index');
            Route::post('/health/visit', [OperationsController::class, 'healthVisitStore'])->name('health.visit.store');
            Route::post('/discipline/case', [OperationsController::class, 'disciplineCaseStore'])->name('discipline.case.store');
        });
    });

    // Broadcast Blast
    Route::prefix('school/communication')->name('school.communication.')->middleware(['module:communication'])->group(function () {
        Route::get('/dashboard', [CommunicationController::class, 'index'])->name('index');
        Route::post('/blast', [CommunicationController::class, 'sendBlast'])->name('send-blast');
        Route::post('/templates', [CommunicationController::class, 'storeTemplate'])->name('templates.store');
        Route::delete('/templates/{id}', [CommunicationController::class, 'destroyTemplate'])->name('templates.destroy');
    });

    // Phase 12 - Documentation & Deployment Guides (Protected)
    Route::middleware(['auth', 'super_admin'])->group(function () {
        Route::get('/school/docs/deployment', [\App\Http\Controllers\School\DocsController::class, 'deployment'])->name('school.docs.deployment');
        Route::get('/school/docs/testing', [\App\Http\Controllers\School\DocsController::class, 'testing'])->name('school.docs.testing');
        Route::get('/school/docs/security', [\App\Http\Controllers\School\DocsController::class, 'security'])->name('school.docs.security');
    });

    Route::middleware(['permission:manage-settings'])->group(function () {
        Route::get('/school/docs/help', [\App\Http\Controllers\School\DocsController::class, 'help'])->name('school.docs.help');
    });

    Route::get('/school/global-search', [SearchController::class, 'globalSearch'])->name('school.global-search');

    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user) {
            if ($user->role) {
                if ($user->role->slug === 'student') {
                    return redirect()->route('school.student-portal.dashboard');
                }
                if ($user->role->slug === 'parent') {
                    return redirect()->route('school.parent-portal.dashboard');
                }
            }
        }
        return view('school.dashboard');
    })->name('dashboard');
});

// Global /dashboard redirect when accessed without a school path prefix
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user && $user->school_id) {
        $school = \App\Models\School::find($user->school_id);
        if ($school) {
            $path = '/dashboard';
            if (!$school->onboarding_completed) {
                $path = '/school/onboarding';
            } elseif ($user->role) {
                if ($user->role->slug === 'student') {
                    $path = '/school/student-portal/dashboard';
                } elseif ($user->role->slug === 'parent') {
                    $path = '/school/parent-portal/dashboard';
                }
            }
            return redirect()->to('/' . $school->subdomain . $path);
        }
    }
    return redirect()->route('login');
})->middleware(['auth']);

// Public Report Verification (No Auth)
Route::get('/public/verify-report/{hash}', [ReportCardController::class, 'publicVerify'])->name('public.verify-report');

// Tenant Branding Preview Stylesheet (Safe / Public access cache)
Route::get('school/website/branding-{schoolId}.css', [PublicWebsiteController::class, 'brandingCss'])->name('school.website.branding-css');

// Public Tenant Domain Pages (Tenant Aware - No Auth)
Route::middleware([\App\Http\Middleware\IdentifyTenant::class])->group(function () {
    Route::get('/public-site/{slug?}', [PublicWebsiteController::class, 'showPage'])->name('public.site.page');
    Route::post('/public-site/contact', [PublicWebsiteController::class, 'contactSubmit'])->name('public.site.contact');
    
    // Dynamic block API feeds
    Route::get('/api/public/news', [PublicWebsiteController::class, 'newsFeed']);
    Route::get('/api/public/events', [PublicWebsiteController::class, 'eventsFeed']);
    Route::get('/api/public/staff', [PublicWebsiteController::class, 'staffFeed']);
    Route::get('/api/public/gallery', [PublicWebsiteController::class, 'galleryFeed']);

    // Path-based Professional Subdomain routes (wildcard fallbacks)
    Route::get('/{school_subdomain}/{slug?}', [PublicWebsiteController::class, 'showPageByPath'])->name('public.site.path.page');
    Route::post('/{school_subdomain}/contact', [PublicWebsiteController::class, 'contactSubmitByPath'])->name('public.site.path.contact');
});

// Global Notification Actions (Available to all logged-in portal users)
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\UserController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\UserController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
});

