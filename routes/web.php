<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountReportsController;
use App\Http\Controllers\AccountTypeController;
// use App\Http\Controllers\Auth;
use App\Http\Controllers\BackUpController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessLocationController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CombinedPurchaseReturnController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerGroupController;
use App\Http\Controllers\DashboardConfiguratorController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\DocumentAndNoteController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GroupTaxController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportOpeningStockController;
use App\Http\Controllers\ImportProductsController;
use App\Http\Controllers\ImportSalesController;
use App\Http\Controllers\Install;
use App\Http\Controllers\InvoiceLayoutController;
use App\Http\Controllers\InvoiceSchemeController;
use App\Http\Controllers\LabelsController;
use App\Http\Controllers\LedgerDiscountController;
use App\Http\Controllers\LocationSettingsController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\OpeningStockController;
use App\Http\Controllers\PaymentAccountController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Restaurant;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesCommissionAgentController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\SellingPriceGroupController;
use App\Http\Controllers\SellPosController;
use App\Http\Controllers\SellReturnController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\TransactionPaymentController;
use App\Http\Controllers\TypesOfServiceController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VariationTemplateController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\SaasPricingController;
use App\Http\Controllers\SaasAdminController;
use App\Http\Controllers\CloudSyncController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobTemplateController;
use App\Http\Controllers\EtimsReportController;
use App\Http\Controllers\HospitalBillingController;
use App\Http\Controllers\Parcel\ParcelController;
use App\Http\Controllers\Parcel\ParcelRouteController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include_once 'install_r.php';

// ─── SaaS Public Routes ───────────────────────────────────────────────────
Route::middleware(['setData'])->group(function () {
    Route::get('/pricing',           [SaasPricingController::class, 'index'])->name('saas.pricing');
    Route::post('/pricing/calculate',[SaasPricingController::class, 'calculate'])->name('saas.calculate');
    Route::get('/pricing/checkout',  [SaasPricingController::class, 'checkout'])->name('saas.checkout');
    Route::post('/pricing/order',    [SaasPricingController::class, 'submitOrder'])->name('saas.order.submit');
});

// Daraja STK callback — Safaricom posts here, must be public (no auth, no CSRF)
Route::post('/saas/mpesa/callback/{invoice}', [SaasPricingController::class, 'mpesaCallback'])
    ->name('saas.mpesa.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// ─── Onboarding M-Pesa callback (no auth, no CSRF) ────────────────────────
Route::post('/onboarding/mpesa/callback', [OnboardingController::class, 'mpesaCallback'])
    ->name('onboarding.mpesa.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// ─── Onboarding (post-registration activation) ────────────────────────────
Route::middleware(['auth', 'SetSessionData'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/activate',       [OnboardingController::class, 'showActivate'])->name('activate');
    Route::post('/trial',         [OnboardingController::class, 'startTrial'])->name('trial');
    Route::post('/stk-push',      [OnboardingController::class, 'initiateStkPush'])->name('stk_push');
    Route::get('/check-payment',  [OnboardingController::class, 'checkPaymentStatus'])->name('check_payment');
});

// ─── SaaS Customer Portal ─────────────────────────────────────────────────
Route::middleware(['setData', 'auth', 'SetSessionData'])->group(function () {
    Route::get('/my-subscription', [SaasPricingController::class, 'portal'])->name('saas.portal');
    Route::get('/pricing/mpesa-pending/{invoice}', [SaasPricingController::class, 'mpesaPending'])->name('saas.mpesa.pending');
    Route::get('/pricing/mpesa-status/{invoice}',  [SaasPricingController::class, 'mpesaStatus'])->name('saas.mpesa.status');
});

// ─── Cloud Sync Routes ────────────────────────────────────────────────────
// Public token-authenticated API endpoints (no CSRF — secured by X-Sync-Token)
Route::prefix('sync')->name('sync.')->group(function () {
    // Device registration requires a logged-in user
    Route::post('/register', [CloudSyncController::class, 'register'])
        ->middleware(['auth', 'SetSessionData'])
        ->name('register');

    // Pull / Push / Status use the device token instead of session auth
    Route::post('/pull',     [CloudSyncController::class, 'pull'])->name('pull');
    Route::post('/push',     [CloudSyncController::class, 'push'])->name('push');
    Route::get('/status',    [CloudSyncController::class, 'status'])->name('status');
    // Sync settings (saved to storage, readable by artisan sync:pull)
    Route::get('/settings',       [CloudSyncController::class, 'getSettings'])->middleware(['auth'])->name('settings.get');
    Route::post('/settings',      [CloudSyncController::class, 'saveSettings'])->middleware(['auth'])->name('settings.save');
    // Remote pull/push: server-to-server sync (Laragon → live server, no terminal needed)
    Route::post('/pull-remote',   [CloudSyncController::class, 'pullRemote'])->middleware(['auth'])->name('pull.remote');
    Route::post('/push-remote',   [CloudSyncController::class, 'pushRemote'])->middleware(['auth'])->name('push.remote');
    // CORS pre-flight for cross-domain requests (Laragon → live server)
    Route::options('/{any}', [CloudSyncController::class, 'preflight'])->where('any', '.*');

    // Dashboard is handled inside the full-auth group (see ~line 985) to get
    // AdminSidebarMenu + CheckUserLogin middleware like all other dashboard pages.
});

// Exempt push/pull from CSRF (they use the device token header instead)
// ─── SaaS Superadmin Routes ───────────────────────────────────────────────
Route::prefix('saas-admin')->name('saas.admin.')->middleware(['setData', 'auth', 'SetSessionData'])->group(function () {
    Route::get('/',                   [SaasAdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/features',           [SaasAdminController::class, 'featuresIndex'])->name('features');
    Route::get('/features/create',    [SaasAdminController::class, 'featuresCreate'])->name('features.create');
    Route::post('/features',          [SaasAdminController::class, 'featuresStore'])->name('features.store');
    Route::get('/features/{feature}/edit', [SaasAdminController::class, 'featuresEdit'])->name('features.edit');
    Route::put('/features/{feature}', [SaasAdminController::class, 'featuresUpdate'])->name('features.update');
    Route::delete('/features/{feature}', [SaasAdminController::class, 'featuresDestroy'])->name('features.destroy');
    Route::post('/features/{feature}/toggle', [SaasAdminController::class, 'featuresToggle'])->name('features.toggle');
    Route::post('/features/{feature}/price',  [SaasAdminController::class, 'featuresUpdatePrice'])->name('features.price');

    Route::get('/enquiries',          [SaasAdminController::class, 'enquiriesIndex'])->name('enquiries');
    Route::post('/enquiries/{id}/status', [SaasAdminController::class, 'enquiriesUpdateStatus'])->name('enquiries.status');

    Route::get('/bundles',            [SaasAdminController::class, 'bundlesIndex'])->name('bundles');
    Route::get('/bundles/create',     [SaasAdminController::class, 'bundlesCreate'])->name('bundles.create');
    Route::post('/bundles',           [SaasAdminController::class, 'bundlesStore'])->name('bundles.store');
    Route::get('/bundles/{bundle}/edit', [SaasAdminController::class, 'bundlesEdit'])->name('bundles.edit');
    Route::put('/bundles/{bundle}',   [SaasAdminController::class, 'bundlesUpdate'])->name('bundles.update');
    Route::delete('/bundles/{bundle}',[SaasAdminController::class, 'bundlesDestroy'])->name('bundles.destroy');

    Route::get('/subscriptions',      [SaasAdminController::class, 'subscriptionsIndex'])->name('subscriptions');
    Route::get('/subscriptions/{subscription}', [SaasAdminController::class, 'subscriptionsShow'])->name('subscriptions.show');
    Route::post('/subscriptions/{subscription}/status', [SaasAdminController::class, 'subscriptionsUpdateStatus'])->name('subscriptions.status');
    Route::post('/subscriptions/{subscription}/extend', [SaasAdminController::class, 'subscriptionsExtend'])->name('subscriptions.extend');

    Route::get('/invoices',           [SaasAdminController::class, 'invoicesIndex'])->name('invoices');
    Route::post('/invoices/{invoice}/paid', [SaasAdminController::class, 'invoicesMarkPaid'])->name('invoices.mark_paid');

    // Per-client feature enable/disable
    Route::post('/subscriptions/{subscription}/feature/attach', [SaasAdminController::class, 'subscriptionFeatureAttach'])->name('subscriptions.feature.attach');
    Route::delete('/subscriptions/{subscription}/feature/{feature}', [SaasAdminController::class, 'subscriptionFeatureDetach'])->name('subscriptions.feature.detach');

    // Business management
    Route::get('/business',                              [SaasAdminController::class, 'businessIndex'])->name('business');
    Route::get('/business/{business_id}/features',       [SaasAdminController::class, 'businessFeatures'])->name('business.features');
    Route::post('/business/{business_id}/features',      [SaasAdminController::class, 'updateBusinessFeatures'])->name('business.features.update');
    Route::get('/business/{business_id}/login-screen',   [SaasAdminController::class, 'businessLoginScreen'])->name('business.login-screen');
    Route::post('/business/{business_id}/login-screen',  [SaasAdminController::class, 'saveBusinessLoginScreen'])->name('business.login-screen.save');

    // Global SaaS settings (trial, campaign, payment)
    Route::get('/saas-settings',  [SaasAdminController::class, 'settingsIndex'])->name('saas_settings');
    Route::put('/saas-settings',  [SaasAdminController::class, 'settingsUpdate'])->name('settings.update');

    // Platform-level key/value settings (allow_registration, etc.)
    Route::post('/setting', [SaasAdminController::class, 'saveSetting'])->name('setting');
});

Route::middleware(['setData'])->group(function () {
    Route::get('/', function () {
        $featuresByCategory = \App\SaasFeature::activeByCategory();
        $bundles = \App\SaasBundle::with('features')->where('is_active', true)->orderBy('sort_order')->get();

        $categories = [
            'core'          => ['label' => 'Core (Always Included)', 'icon' => 'fa-star'],
            'inventory'     => ['label' => 'Inventory & Stock',       'icon' => 'fa-boxes'],
            'pharmacy'      => ['label' => 'Pharmacy / DDA',          'icon' => 'fa-pills'],
            'compliance'    => ['label' => 'Tax & Compliance',        'icon' => 'fa-file-invoice'],
            'reporting'     => ['label' => 'Reports & Analytics',     'icon' => 'fa-chart-bar'],
            'communication' => ['label' => 'Communication',           'icon' => 'fa-comment-dots'],
            'restaurant'    => ['label' => 'Restaurant',              'icon' => 'fa-utensils'],
            'service'       => ['label' => 'Professional Services',   'icon' => 'fa-tools'],
        ];

        return view('welcome', compact('featuresByCategory', 'bundles', 'categories'));
    });

    Auth::routes();

    Route::get('/business/register', [BusinessController::class, 'getRegister'])->name('business.getRegister');
    Route::post('/business/register', [BusinessController::class, 'postRegister'])->name('business.postRegister');
    Route::post('/business/register/check-username', [BusinessController::class, 'postCheckUsername'])->name('business.postCheckUsername');
    Route::post('/business/register/check-email', [BusinessController::class, 'postCheckEmail'])->name('business.postCheckEmail');

    Route::get('/invoice/{token}', [SellPosController::class, 'showInvoice'])
        ->name('show_invoice');
    Route::get('/quote/{token}', [SellPosController::class, 'showInvoice'])
        ->name('show_quote');

    Route::get('/pay/{token}', [SellPosController::class, 'invoicePayment'])
        ->name('invoice_payment');
    Route::post('/confirm-payment/{id}', [SellPosController::class, 'confirmPayment'])
        ->name('confirm_payment');
});

//Routes for authenticated users only
Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
    // Moved to very top to avoid any conflict
    Route::get('/business/settings', [BusinessController::class, 'getBusinessSettings'])->name('business.getBusinessSettings');
    Route::post('/business/update', [BusinessController::class, 'postBusinessSettings'])->name('business.postBusinessSettings');

    // ── Approval System ────────────────────────────────────────────
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/',                                      [\App\Http\Controllers\ApprovalController::class, 'index'])->name('index');
        Route::post('/{approval}/decide',                   [\App\Http\Controllers\ApprovalController::class, 'decide'])->name('decide');
        Route::post('/{approval}/resubmit',                 [\App\Http\Controllers\ApprovalController::class, 'resubmit'])->name('resubmit');
        Route::get('/flows',                                 [\App\Http\Controllers\ApprovalController::class, 'flows'])->name('flows');
        Route::post('/flows',                                [\App\Http\Controllers\ApprovalController::class, 'storeFlow'])->name('flows.store');
        Route::delete('/flows/{flow}',                       [\App\Http\Controllers\ApprovalController::class, 'destroyFlow'])->name('flows.destroy');
    });

    // BI Dashboard & Settings
    Route::get('/dashboard/bi', [\App\Http\Controllers\BIDashboardController::class, 'index'])->name('dashboard.bi');
    Route::post('/dashboard/bi/settings', [\App\Http\Controllers\BIDashboardController::class, 'saveSettings'])->name('dashboard.bi.settings.save');
    
    // BI AJAX Routes (for Apex AI)
    Route::get('/bi-api/insights', [\App\Http\Controllers\BIDashboardController::class, 'getInsights']);
    Route::post('/bi-api/ask', [\App\Http\Controllers\BIDashboardController::class, 'askQuestion']);
    Route::post('/bi-api/generate-campaign', [\App\Http\Controllers\BIDashboardController::class, 'generateCampaign']);
    Route::post('/bi-api/run-procurement', [\App\Http\Controllers\BIDashboardController::class, 'runAutoProcurement']);
    Route::get('/bi-api/predictions', [\App\Http\Controllers\BIDashboardController::class, 'getPredictions']);
    Route::get('/bi-api/deep-intelligence', [\App\Http\Controllers\BIDashboardController::class, 'getDeepIntelligence']);
    
    // BI Analytics Data APIs
    Route::get('/bi-api/sales',     [\App\Http\Controllers\BIDashboardController::class, 'getSalesAnalytics']);
    Route::get('/bi-api/customers', [\App\Http\Controllers\BIDashboardController::class, 'getCustomerAnalytics']);
    Route::get('/bi-api/financial', [\App\Http\Controllers\BIDashboardController::class, 'getFinancialKpis']);
    Route::get('/bi-api/inventory', [\App\Http\Controllers\BIDashboardController::class, 'getInventoryAnalytics']);

    // AI Intelligence APIs (Gemini-powered)
    Route::get('/bi-api/ai/procurement', [\App\Http\Controllers\BIDashboardController::class, 'getAIProcurement']);
    Route::get('/bi-api/ai/customers',   [\App\Http\Controllers\BIDashboardController::class, 'getAICustomerInsights']);
    Route::get('/bi-api/ai/sales',       [\App\Http\Controllers\BIDashboardController::class, 'getAISalesInsights']);
    Route::get('/bi-api/ai/financial',   [\App\Http\Controllers\BIDashboardController::class, 'getAIFinancialAdvice']);
    Route::get('/bi-api/ai/inventory',   [\App\Http\Controllers\BIDashboardController::class, 'getAIInventoryHealth']);

    // Advanced AI Routes
    Route::get('/bi-api/customer-segments', [\App\Http\Controllers\Hospital\CustomerIntelligenceController::class, 'getSegments']);
    Route::get('/bi-api/patient-forecast', [\App\Http\Controllers\Hospital\CustomerIntelligenceController::class, 'getVolumeForecast']);
    Route::get('service-staff-availability', [SellPosController::class, 'showServiceStaffAvailibility']);
    Route::get('pause-resume-service-staff-timer/{user_id}', [SellPosController::class, 'pauseResumeServiceStaffTimer']);
    Route::get('mark-as-available/{user_id}', [SellPosController::class, 'markAsAvailable']);
    Route::resource('pos', SellPosController::class);

    Route::resource('purchase-requisition', PurchaseRequisitionController::class)->except(['edit', 'update']);
    Route::post('/get-requisition-products', [PurchaseRequisitionController::class, 'getRequisitionProducts'])->name('get-requisition-products');
    Route::get('get-purchase-requisitions/{location_id}', [PurchaseRequisitionController::class, 'getPurchaseRequisitions']);
    Route::get('get-purchase-requisition-lines/{purchase_requisition_id}', [PurchaseRequisitionController::class, 'getPurchaseRequisitionLines']);

    Route::get('/sign-in-as-user/{id}', [ManageUserController::class, 'signInAsUser'])->name('sign-in-as-user');

    //Hospital routes — gated by SaaS "hospital" feature + active subscription
    Route::middleware('feature:hospital')->group(function () {
    Route::get('/hospital', [\App\Http\Controllers\Hospital\HospitalController::class, 'index'])->name('hospital.index');

    // Patients
    Route::get('/hospital/patients', [\App\Http\Controllers\Hospital\PatientController::class, 'index'])->name('hospital.patients.index');
    Route::get('/hospital/patients/create', [\App\Http\Controllers\Hospital\PatientController::class, 'create'])->name('hospital.patients.create');
    Route::post('/hospital/patients', [\App\Http\Controllers\Hospital\PatientController::class, 'store'])->name('hospital.patients.store');
    Route::get('/hospital/patients/{id}', [\App\Http\Controllers\Hospital\PatientController::class, 'show'])->name('hospital.patients.show');
    Route::get('/hospital/create-appointment', [\App\Http\Controllers\Hospital\HospitalController::class, 'createAppointment'])->name('hospital.createAppointment');
    Route::post('/hospital/store-appointment', [\App\Http\Controllers\Hospital\HospitalController::class, 'storeAppointment'])->name('hospital.storeAppointment');
    Route::get('/hospital/flow', [\App\Http\Controllers\Hospital\HospitalController::class, 'flowDashboard'])->name('hospital.flow');
    Route::get('/hospital/triage/{id}', [\App\Http\Controllers\Hospital\HospitalController::class, 'triage'])->name('hospital.triage');
    Route::post('/hospital/store-triage', [\App\Http\Controllers\Hospital\HospitalController::class, 'storeTriage'])->name('hospital.storeTriage');
    Route::get('/hospital/consultation/{id}', [\App\Http\Controllers\Hospital\HospitalController::class, 'consultation'])->name('hospital.consultation');
    Route::get('/hospital/consult-walkin/{id}', [\App\Http\Controllers\Hospital\HospitalController::class, 'consultWalkIn'])->name('hospital.consultWalkIn');
    Route::post('/hospital/store-consultation', [\App\Http\Controllers\Hospital\HospitalController::class, 'storeConsultation'])->name('hospital.storeConsultation');

    // IPD routes
    Route::get('/hospital/ipd', [\App\Http\Controllers\Hospital\HospitalController::class, 'ipdIndex'])->name('hospital.ipdIndex');
    Route::get('/hospital/ipd/create-admission', [\App\Http\Controllers\Hospital\HospitalController::class, 'createAdmission'])->name('hospital.createAdmission');
    Route::post('/hospital/ipd/store-admission', [\App\Http\Controllers\Hospital\HospitalController::class, 'storeAdmission'])->name('hospital.storeAdmission');
    Route::get('/hospital/ipd/discharge/{id}', [\App\Http\Controllers\Hospital\HospitalController::class, 'dischargePatient'])->name('hospital.dischargePatient');
    Route::get('/hospital/ipd/add-daily-record/{id}', [\App\Http\Controllers\Hospital\HospitalController::class, 'addDailyRecord'])->name('hospital.addDailyRecord');
    Route::post('/hospital/ipd/store-daily-record', [\App\Http\Controllers\Hospital\HospitalController::class, 'storeDailyRecord'])->name('hospital.storeDailyRecord');
    Route::get('/hospital/search-drugs', [\App\Http\Controllers\Hospital\HospitalController::class, 'searchDrugs'])->name('hospital.searchDrugs');

    //Lab routes
    Route::get('/hospital/lab', [\App\Http\Controllers\Hospital\LabController::class, 'index'])->name('hospital.lab.index');
    Route::get('/hospital/lab/create-test', [\App\Http\Controllers\Hospital\LabController::class, 'createTest'])->name('hospital.lab.createTest');
    Route::post('/hospital/lab/store-test', [\App\Http\Controllers\Hospital\LabController::class, 'storeTest'])->name('hospital.lab.storeTest');
    Route::get('/hospital/lab/enter-result/{id}', [\App\Http\Controllers\Hospital\LabController::class, 'enterResult'])->name('hospital.lab.enterResult');
    Route::post('/hospital/lab/store-result', [\App\Http\Controllers\Hospital\LabController::class, 'storeResult'])->name('hospital.lab.storeResult');

    //Radiography routes
    Route::get('/hospital/radiography', [\App\Http\Controllers\Hospital\RadiographyController::class, 'index'])->name('hospital.radiography.index');
    Route::get('/hospital/radiography/create-test', [\App\Http\Controllers\Hospital\RadiographyController::class, 'createTest'])->name('hospital.radiography.createTest');
    Route::post('/hospital/radiography/store-test', [\App\Http\Controllers\Hospital\RadiographyController::class, 'storeTest'])->name('hospital.radiography.storeTest');
    Route::get('/hospital/radiography/enter-result/{id}', [\App\Http\Controllers\Hospital\RadiographyController::class, 'enterResult'])->name('hospital.radiography.enterResult');
    Route::post('/hospital/radiography/store-result', [\App\Http\Controllers\Hospital\RadiographyController::class, 'storeResult'])->name('hospital.radiography.storeResult');

    //Theatre/Surgery routes
    Route::get('/hospital/theatre', [\App\Http\Controllers\Hospital\TheatreController::class, 'index'])->name('hospital.theatre.index');
    Route::get('/hospital/theatre/create-booking', [\App\Http\Controllers\Hospital\TheatreController::class, 'createBooking'])->name('hospital.theatre.createBooking');
    Route::post('/hospital/theatre/store-booking', [\App\Http\Controllers\Hospital\TheatreController::class, 'storeBooking'])->name('hospital.theatre.storeBooking');
    Route::get('/hospital/theatre/edit-record/{id}', [\App\Http\Controllers\Hospital\TheatreController::class, 'editRecord'])->name('hospital.theatre.editRecord');
    Route::post('/hospital/theatre/update-record/{id}', [\App\Http\Controllers\Hospital\TheatreController::class, 'updateRecord'])->name('hospital.theatre.updateRecord');

    //Physiotherapy routes
    Route::get('/hospital/physio', [\App\Http\Controllers\Hospital\PhysiotherapyController::class, 'index'])->name('hospital.physio.index');
    Route::get('/hospital/physio/create-plan', [\App\Http\Controllers\Hospital\PhysiotherapyController::class, 'createPlan'])->name('hospital.physio.createPlan');
    Route::post('/hospital/physio/store-plan', [\App\Http\Controllers\Hospital\PhysiotherapyController::class, 'storePlan'])->name('hospital.physio.storePlan');
    Route::get('/hospital/physio/add-session/{plan_id}', [\App\Http\Controllers\Hospital\PhysiotherapyController::class, 'addSession'])->name('hospital.physio.addSession');
    Route::post('/hospital/physio/store-session', [\App\Http\Controllers\Hospital\PhysiotherapyController::class, 'storeSession'])->name('hospital.physio.storeSession');

    //Mortuary routes
    Route::get('/hospital/mortuary', [\App\Http\Controllers\Hospital\MortuaryController::class, 'index'])->name('hospital.mortuary.index');
    Route::get('/hospital/mortuary/create', [\App\Http\Controllers\Hospital\MortuaryController::class, 'create'])->name('hospital.mortuary.create');
    Route::post('/hospital/mortuary/store', [\App\Http\Controllers\Hospital\MortuaryController::class, 'store'])->name('hospital.mortuary.store');
    Route::get('/hospital/mortuary/release/{id}', [\App\Http\Controllers\Hospital\MortuaryController::class, 'release'])->name('hospital.mortuary.release');

    //Inpatient Nursing & Fluid Balance
    Route::get('/hospital/inpatient', [\App\Http\Controllers\Hospital\InpatientController::class, 'index'])->name('hospital.inpatient.index');
    Route::get('/hospital/inpatient/admission/{id}', [\App\Http\Controllers\Hospital\InpatientController::class, 'showAdmission'])->name('hospital.inpatient.show');
    Route::post('/hospital/inpatient/add-nursing-note', [\App\Http\Controllers\Hospital\InpatientController::class, 'addNursingNote'])->name('hospital.inpatient.addNursingNote');
    Route::get('/hospital/inpatient/discharge/{id}', [\App\Http\Controllers\Hospital\InpatientController::class, 'discharge'])->name('hospital.inpatient.discharge');

    //EHR Timeline
    Route::get('/hospital/patient-timeline/{id}', [\App\Http\Controllers\Hospital\PatientTimelineController::class, 'show'])->name('hospital.patient.timeline');

    //Billing routes
    Route::get('/hospital/billing', [\App\Http\Controllers\Hospital\HospitalBillingController::class, 'index'])->name('hospital.billing.index');
    Route::get('/hospital/billing/patient-bill/{id}', [\App\Http\Controllers\Hospital\HospitalBillingController::class, 'patientBill'])->name('hospital.billing.patientBill');
    Route::post('/hospital/billing/create-invoice', [\App\Http\Controllers\Hospital\HospitalBillingController::class, 'createInvoice'])->name('hospital.billing.createInvoice');

    //Queue routes
    Route::get('/hospital/queue', [\App\Http\Controllers\Hospital\HospitalQueueController::class, 'index'])->name('hospital.queue.index');
    Route::get('/hospital/queue/live', [\App\Http\Controllers\Hospital\HospitalQueueController::class, 'liveDisplay'])->name('hospital.queue.liveDisplay');
    Route::post('/hospital/queue/add', [\App\Http\Controllers\Hospital\HospitalQueueController::class, 'addToQueue'])->name('hospital.queue.addToQueue');
    Route::get('/hospital/queue/move', [\App\Http\Controllers\Hospital\HospitalQueueController::class, 'movePatient'])->name('hospital.queue.movePatient');
    Route::get('/hospital/queue/update-status', [\App\Http\Controllers\Hospital\HospitalQueueController::class, 'updateStatus'])->name('hospital.queue.updateStatus');

    //Report routes
    Route::get('/hospital/reports/moh705', [\App\Http\Controllers\Hospital\HospitalReportController::class, 'moh705Index'])->name('hospital.reports.moh705Index');
    Route::get('/hospital/reports/moh705A', [\App\Http\Controllers\Hospital\HospitalReportController::class, 'moh705A'])->name('hospital.reports.moh705A');
    Route::get('/hospital/reports/moh705B', [\App\Http\Controllers\Hospital\HospitalReportController::class, 'moh705B'])->name('hospital.reports.moh705B');

    //Dental routes
    Route::get('/hospital/dental/{patient_id}', [\App\Http\Controllers\Hospital\DentalController::class, 'index'])->name('hospital.dental.index');
    Route::post('/hospital/dental/update-tooth', [\App\Http\Controllers\Hospital\DentalController::class, 'updateTooth'])->name('hospital.dental.updateTooth');
    Route::post('/hospital/dental/add-procedure', [\App\Http\Controllers\Hospital\DentalController::class, 'addProcedure'])->name('hospital.dental.addProcedure');

    //Asset routes
    Route::get('/hospital/assets', [\App\Http\Controllers\Hospital\HospitalAssetController::class, 'index'])->name('hospital.assets.index');
    Route::get('/hospital/assets/create', [\App\Http\Controllers\Hospital\HospitalAssetController::class, 'create'])->name('hospital.assets.create');
    Route::post('/hospital/assets/store', [\App\Http\Controllers\Hospital\HospitalAssetController::class, 'store'])->name('hospital.assets.store');
    Route::get('/hospital/assets/maintenance/{id}', [\App\Http\Controllers\Hospital\HospitalAssetController::class, 'addMaintenance'])->name('hospital.assets.addMaintenance');
    Route::post('/hospital/assets/store-maintenance', [\App\Http\Controllers\Hospital\HospitalAssetController::class, 'storeMaintenance'])->name('hospital.assets.storeMaintenance');

    //Maternity routes
    Route::get('/hospital/maternity', [\App\Http\Controllers\Hospital\MaternityController::class, 'index'])->name('hospital.maternity.index');
    Route::get('/hospital/maternity/create-profile', [\App\Http\Controllers\Hospital\MaternityController::class, 'createProfile'])->name('hospital.maternity.createProfile');
    Route::post('/hospital/maternity/store-profile', [\App\Http\Controllers\Hospital\MaternityController::class, 'storeProfile'])->name('hospital.maternity.storeProfile');
    Route::get('/hospital/maternity/profile/{id}', [\App\Http\Controllers\Hospital\MaternityController::class, 'showProfile'])->name('hospital.maternity.showProfile');
    Route::post('/hospital/maternity/store-anc-visit', [\App\Http\Controllers\Hospital\MaternityController::class, 'storeAncVisit'])->name('hospital.maternity.storeAncVisit');

    //Pharmacy routes
    Route::get('/hospital/pharmacy', [\App\Http\Controllers\Hospital\PharmacyController::class, 'index'])->name('hospital.pharmacy.index');
    Route::get('/hospital/pharmacy/dispense/{patient_id}', [\App\Http\Controllers\Hospital\PharmacyController::class, 'dispense'])->name('hospital.pharmacy.dispense');
    Route::post('/hospital/pharmacy/store-dispense', [\App\Http\Controllers\Hospital\PharmacyController::class, 'storeDispense'])->name('hospital.pharmacy.storeDispense');
    }); // end feature:hospital group

    Route::get('/home', [HomeController::class, 'index'])->middleware('trial')->name('home');
    Route::get('/home/get-totals', [HomeController::class, 'getTotals']);
    Route::get('/home/live-stats', [HomeController::class, 'getLiveStats']);
    Route::get('/home/best-sellers', [HomeController::class, 'getBestSellers']);
    Route::get('/home/expiring-products', [HomeController::class, 'getExpiringProducts']);
    Route::get('/home/reorder-suggestions', [HomeController::class, 'getReorderSuggestions']);
    Route::get('/home/product-stock-alert', [HomeController::class, 'getProductStockAlert']);
    Route::get('/home/purchase-payment-dues', [HomeController::class, 'getPurchasePaymentDues']);
    Route::get('/home/sales-payment-dues', [HomeController::class, 'getSalesPaymentDues']);
    Route::get('/home/dashboard-counters', [HomeController::class, 'getDashboardCounters']);
    Route::get('/home/overall-reports', [HomeController::class, 'getOverallReports']);
    Route::post('/attach-medias-to-model', [HomeController::class, 'attachMediasToGivenModel'])->name('attach.medias.to.model');
    Route::get('/calendar', [HomeController::class, 'getCalendar'])->name('calendar');

    // Members - Coming Soon
    Route::get('/members', [\App\Http\Controllers\MemberController::class, 'index'])->name('members.index');

    Route::post('/test-email', [BusinessController::class, 'testEmailConfiguration']);
    Route::post('/test-sms', [BusinessController::class, 'testSmsConfiguration']);
    Route::post('/test-whatsapp', [BusinessController::class, 'testWhatsAppConfiguration']);

    // SMS Module
    Route::get('/sms/send', [\App\Http\Controllers\SmsController::class, 'sendForm'])->name('sms.send');
    Route::post('/sms/send', [\App\Http\Controllers\SmsController::class, 'send']);
    Route::get('/sms/history', [\App\Http\Controllers\SmsController::class, 'history'])->name('sms.history');
    Route::get('/sms/automated', [\App\Http\Controllers\SmsController::class, 'automated'])->name('sms.automated');

    // DDA (Dangerous Drugs Act) Module
    Route::get('/dda', [\App\Http\Controllers\DdaController::class, 'dashboard'])->name('dda.dashboard');
    Route::get('/dda/drugs', [\App\Http\Controllers\DdaController::class, 'drugs'])->name('dda.drugs');
    Route::post('/dda/drugs', [\App\Http\Controllers\DdaController::class, 'storeDrug'])->name('dda.drugs.store');
    Route::put('/dda/drugs/{id}', [\App\Http\Controllers\DdaController::class, 'updateDrug'])->name('dda.drugs.update');
    Route::get('/dda/products', [\App\Http\Controllers\DdaController::class, 'products'])->name('dda.products');
    Route::get('/dda/prescriptions', [\App\Http\Controllers\DdaController::class, 'prescriptions'])->name('dda.prescriptions');
    Route::get('/dda/prescriptions/{id}/view', [\App\Http\Controllers\DdaController::class, 'viewPrescription'])->name('dda.prescriptions.view');
    Route::get('/dda/prescriptions/create', [\App\Http\Controllers\DdaController::class, 'createPrescription'])->name('dda.prescriptions.create');
    Route::post('/dda/prescriptions', [\App\Http\Controllers\DdaController::class, 'storePrescription'])->name('dda.prescriptions.store');
    Route::get('/dda/dispense', [\App\Http\Controllers\DdaController::class, 'dispenseRegister'])->name('dda.dispense');
    Route::post('/dda/dispense', [\App\Http\Controllers\DdaController::class, 'storeDispense'])->name('dda.dispense.store');
    Route::get('/dda/stock', [\App\Http\Controllers\DdaController::class, 'stockBalance'])->name('dda.stock');
    Route::post('/dda/stock', [\App\Http\Controllers\DdaController::class, 'storeStockLog'])->name('dda.stock.store');
    Route::post('/dda/prescription/pos-upload', [\App\Http\Controllers\DdaController::class, 'posUploadPrescription'])->name('dda.prescription.pos_upload');
    Route::get('/dda/sales', [\App\Http\Controllers\DdaController::class, 'sales'])->name('dda.sales');
    Route::get('/dda/destruction', [\App\Http\Controllers\DdaController::class, 'destruction'])->name('dda.destruction');
    Route::post('/dda/destruction', [\App\Http\Controllers\DdaController::class, 'storeDestruction'])->name('dda.destruction.store');
    Route::post('/dda/destruction/{id}/status', [\App\Http\Controllers\DdaController::class, 'updateDisposalStatus'])->name('dda.destruction.status');
    Route::get('/dda/destruction/{id}/certificate', [\App\Http\Controllers\DdaController::class, 'viewCertificate'])->name('dda.destruction.certificate');
    Route::get('/dda/expired', [\App\Http\Controllers\DdaController::class, 'expiredDrugs'])->name('dda.expired');
    Route::get('/user/profile', [UserController::class, 'getProfile'])->name('user.getProfile');
    Route::post('/user/update', [UserController::class, 'updateProfile'])->name('user.updateProfile');
    Route::post('/user/update-password', [UserController::class, 'updatePassword'])->name('user.updatePassword');

    Route::resource('brands', BrandController::class);

    Route::resource('payment-account', PaymentAccountController::class);

    Route::resource('tax-rates', TaxRateController::class);

    Route::resource('units', UnitController::class);

    Route::resource('ledger-discount', LedgerDiscountController::class)->only('edit', 'destroy', 'store', 'update');

    Route::post('check-mobile', [ContactController::class, 'checkMobile']);
    Route::get('/get-contact-due/{contact_id}', [ContactController::class, 'getContactDue']);
    Route::get('/contacts/credit-info/{contact_id}', [ContactController::class, 'getCustomerCreditInfo']);
    Route::get('/contacts/payments/{contact_id}', [ContactController::class, 'getContactPayments']);
    Route::get('/contacts/map', [ContactController::class, 'contactMap']);
    Route::get('/contacts/update-status/{id}', [ContactController::class, 'updateStatus']);
    Route::get('/contacts/stock-report/{supplier_id}', [ContactController::class, 'getSupplierStockReport']);
    Route::get('/contacts/ledger', [ContactController::class, 'getLedger']);
    Route::post('/contacts/send-ledger', [ContactController::class, 'sendLedger']);
    Route::get('/contacts/import', [ContactController::class, 'getImportContacts'])->name('contacts.import');
    Route::get('/contacts/import/template', [ContactController::class, 'getImportContactsTemplate'])->name('contacts.import.template');
    Route::post('/contacts/import', [ContactController::class, 'postImportContacts']);
    Route::post('/contacts/check-contacts-id', [ContactController::class, 'checkContactId']);

    Route::post('/contacts/check-tax-number', [ContactController::class, 'checkTaxNumber']);

    Route::get('/contacts/customers', [ContactController::class, 'getCustomers']);
    Route::resource('contacts', ContactController::class);

    Route::resource('jobs', JobController::class);
    Route::post('jobs/toggle-checklist/{id}', [JobController::class, 'toggleChecklist']);
    Route::resource('job-categories', JobCategoryController::class);
    Route::resource('job-templates', JobTemplateController::class);

    // ── Cooler Management Module ─────────────────────────────────────────────
    // Route names: cooler.assets.*, cooler.dealers.*, cooler.agreements.*, cooler.retrievals.*, etc.
    Route::prefix('cooler')->name('cooler.')->group(function () {
        // Assets  → cooler.assets.index, .create, .store, .show, .edit, .update, .destroy
        Route::resource('assets', \App\Http\Controllers\CoolerAssetController::class);

        // Dealers → cooler.dealers.*
        Route::resource('dealers', \App\Http\Controllers\CoolerDealerController::class);

        // Agreements → cooler.agreements.*
        Route::resource('agreements', \App\Http\Controllers\CoolerAgreementController::class, ['except' => ['edit', 'update']]);
        Route::get('agreements/{id}/sign',               [\App\Http\Controllers\CoolerAgreementController::class, 'sign'])->name('agreements.sign');
        Route::post('agreements/{id}/sign',              [\App\Http\Controllers\CoolerAgreementController::class, 'captureSignature'])->name('agreements.capture-signature');
        Route::post('agreements/{id}/terminate',         [\App\Http\Controllers\CoolerAgreementController::class, 'terminate'])->name('agreements.terminate');
        Route::get('agreements/{id}/pdf',                [\App\Http\Controllers\CoolerAgreementController::class, 'downloadPdf'])->name('agreements.pdf');

        // Retrievals → cooler.retrievals.*
        Route::resource('retrievals', \App\Http\Controllers\CoolerRetrievalController::class, ['except' => ['edit', 'update']]);
        Route::get('retrievals/{id}/execute',            [\App\Http\Controllers\CoolerRetrievalController::class, 'execute'])->name('retrievals.execute');
        Route::post('retrievals/{id}/upload-photo',      [\App\Http\Controllers\CoolerRetrievalController::class, 'uploadPhoto'])->name('retrievals.upload-photo');
        Route::post('retrievals/{id}/capture-signature', [\App\Http\Controllers\CoolerRetrievalController::class, 'captureSignature'])->name('retrievals.capture-signature');
        Route::post('retrievals/{id}/upload-letter',     [\App\Http\Controllers\CoolerRetrievalController::class, 'uploadSignedLetter'])->name('retrievals.upload-letter');
        Route::post('retrievals/{id}/complete',          [\App\Http\Controllers\CoolerRetrievalController::class, 'complete'])->name('retrievals.complete');
        Route::get('retrievals/{id}/letter',             [\App\Http\Controllers\CoolerRetrievalController::class, 'downloadLetter'])->name('retrievals.letter');
        Route::get('dealer/{dealer}/coolers',            [\App\Http\Controllers\CoolerRetrievalController::class, 'getDealerCoolers'])->name('dealer-coolers');

        // Documents → cooler.documents.*
        Route::get('documents/{id}/view',                [\App\Http\Controllers\CoolerDocumentController::class, 'view'])->name('documents.view');
        Route::get('documents/{id}/download',            [\App\Http\Controllers\CoolerDocumentController::class, 'download'])->name('documents.download');
        Route::post('documents/{id}/verify',             [\App\Http\Controllers\CoolerDocumentController::class, 'verify'])->name('documents.verify');
        Route::post('documents/{id}/reject',             [\App\Http\Controllers\CoolerDocumentController::class, 'reject'])->name('documents.reject');
        Route::delete('documents/{id}',                  [\App\Http\Controllers\CoolerDocumentController::class, 'destroy'])->name('documents.destroy');
        Route::get('documents/expiring-soon',            [\App\Http\Controllers\CoolerDocumentController::class, 'expiringSoon'])->name('documents.expiring-soon');

        // Compliance & Reports
        Route::get('compliance',                         [\App\Http\Controllers\CoolerComplianceController::class, 'dashboard'])->name('compliance.dashboard');
        Route::get('reports',                            [\App\Http\Controllers\CoolerComplianceController::class, 'reports'])->name('reports');
        Route::post('compliance/{dealer}/flag',          [\App\Http\Controllers\CoolerComplianceController::class, 'flagDealer'])->name('compliance.flag');
        Route::post('compliance/{dealer}/recalculate',   [\App\Http\Controllers\CoolerComplianceController::class, 'recalculateScore'])->name('compliance.recalculate');

        // ── Agent Portal ── (requires 'Cooler Agent' role / cooler.agent.portal permission)
        Route::prefix('agent')->name('agent.')->group(function () {
            $ctrl = \App\Http\Controllers\CoolerAgentPortalController::class;

            Route::get('/',                         [$ctrl, 'dashboard'])->name('dashboard');
            // Customers
            Route::get('customers',                 [$ctrl, 'customers'])->name('customers');
            Route::get('customers/create',          [$ctrl, 'createCustomer'])->name('customers.create');
            Route::post('customers',                [$ctrl, 'storeCustomer'])->name('customers.store');
            Route::get('customers/{id}',            [$ctrl, 'showCustomer'])->name('customers.show');
            // Retrievals
            Route::get('retrievals',                [$ctrl, 'retrievals'])->name('retrievals');
            Route::post('retrievals',               [$ctrl, 'storeRetrieval'])->name('retrievals.store');
            // Orders + MPESA
            Route::get('orders/create',             [$ctrl, 'createOrder'])->name('orders.create');
            Route::post('orders',                   [$ctrl, 'storeOrder'])->name('orders.store');
            Route::get('orders/check-payment',      [$ctrl, 'checkPayment'])->name('orders.check_payment');
        });
    });
    // ── End Cooler Management Module ─────────────────────────────────────────

    Route::get('etims-report', [EtimsReportController::class, 'index']);
    Route::get('etims-report/sync-invoice/{id}', [EtimsReportController::class, 'syncInvoice']);
    Route::post('etims-report/sync-all', [EtimsReportController::class, 'syncAll'])->name('etims.sync-all');
    Route::post('products/bulk-etims-sync', [\App\Http\Controllers\ProductController::class, 'bulkEtimsSync']);
    Route::get('etims-settings', [EtimsReportController::class, 'settings'])->name('etims.settings');
    Route::post('etims-settings', [EtimsReportController::class, 'saveSettings'])->name('etims.settings.save');

    Route::get('taxonomies-ajax-index-page', [TaxonomyController::class, 'getTaxonomyIndexPage']);
    Route::resource('taxonomies', TaxonomyController::class);

    Route::resource('variation-templates', VariationTemplateController::class);

    Route::get('/products/download-excel', [ProductController::class, 'downloadExcel']);

    Route::get('/products/stock-history/{id}', [ProductController::class, 'productStockHistory']);
    Route::get('/delete-media/{media_id}', [ProductController::class, 'deleteMedia']);
    Route::post('/products/mass-deactivate', [ProductController::class, 'massDeactivate']);
    Route::get('/products/activate/{id}', [ProductController::class, 'activate']);
    Route::get('/products/view-product-group-price/{id}', [ProductController::class, 'viewGroupPrice']);
    Route::get('/products/add-selling-prices/{id}', [ProductController::class, 'addSellingPrices']);
    Route::post('/products/save-selling-prices', [ProductController::class, 'saveSellingPrices']);
    Route::post('/products/mass-delete', [ProductController::class, 'massDestroy']);
    Route::get('/products/view/{id}', [ProductController::class, 'view']);
    Route::get('/products/list', [ProductController::class, 'getProducts']);
    Route::get('/products/list-no-variation', [ProductController::class, 'getProductsWithoutVariations']);
    Route::post('/products/bulk-edit', [ProductController::class, 'bulkEdit']);
    Route::post('/products/bulk-update', [ProductController::class, 'bulkUpdate']);
    Route::post('/products/bulk-update-location', [ProductController::class, 'updateProductLocation']);
    Route::get('/products/get-product-to-edit/{product_id}', [ProductController::class, 'getProductToEdit']);

    Route::post('/products/get_sub_categories', [ProductController::class, 'getSubCategories']);
    Route::get('/products/get_sub_units', [ProductController::class, 'getSubUnits']);
    Route::post('/products/product_form_part', [ProductController::class, 'getProductVariationFormPart']);
    Route::post('/products/get_product_variation_row', [ProductController::class, 'getProductVariationRow']);
    Route::post('/products/get_variation_template', [ProductController::class, 'getVariationTemplate']);
    Route::get('/products/get_variation_value_row', [ProductController::class, 'getVariationValueRow']);
    Route::post('/products/check_product_sku', [ProductController::class, 'checkProductSku']);
    Route::post('/products/check_product_name', [ProductController::class, 'checkProductName']);
    Route::post('/products/validate_variation_skus', [ProductController::class, 'validateVaritionSkus']); //validates multiple skus at once
    Route::get('/products/quick_add', [ProductController::class, 'quickAdd']);
    Route::post('/products/save_quick_product', [ProductController::class, 'saveQuickProduct']);
    Route::get('/products/get-combo-product-entry-row', [ProductController::class, 'getComboProductEntryRow']);
    Route::post('/products/toggle-woocommerce-sync', [ProductController::class, 'toggleWooCommerceSync']);

    Route::resource('products', ProductController::class);
    Route::get('/sells/copy-quotation/{id}', [SellPosController::class, 'copyQuotation']);

    Route::post('/import-purchase-products', [PurchaseController::class, 'importPurchaseProducts']);
    Route::post('/purchases/update-status', [PurchaseController::class, 'updateStatus']);
    Route::get('/purchases/get_products', [PurchaseController::class, 'getProducts']);
    Route::get('/purchases/get_suppliers', [PurchaseController::class, 'getSuppliers']);
    Route::post('/purchases/get_purchase_entry_row', [PurchaseController::class, 'getPurchaseEntryRow']);
    Route::post('/purchases/check_ref_number', [PurchaseController::class, 'checkRefNumber']);
    Route::resource('purchases', PurchaseController::class)->except(['show']);

    Route::get('/toggle-subscription/{id}', [SellPosController::class, 'toggleRecurringInvoices']);
    Route::post('/sells/pos/get-types-of-service-details', [SellPosController::class, 'getTypesOfServiceDetails']);
    Route::get('/sells/subscriptions', [SellPosController::class, 'listSubscriptions']);
    Route::get('/sells/duplicate/{id}', [SellController::class, 'duplicateSell']);
    Route::get('/sells/drafts', [SellController::class, 'getDrafts']);
    Route::get('/sells/convert-to-draft/{id}', [SellPosController::class, 'convertToInvoice']);
    Route::get('/sells/convert-to-proforma/{id}', [SellPosController::class, 'convertToProforma']);
    Route::get('/sells/quotations', [SellController::class, 'getQuotations']);
    Route::get('/sells/draft-dt', [SellController::class, 'getDraftDatables']);
    Route::resource('sells', SellController::class)->except(['show']);

    Route::get('/import-sales', [ImportSalesController::class, 'index']);
    Route::post('/import-sales/preview', [ImportSalesController::class, 'preview']);
    Route::post('/import-sales', [ImportSalesController::class, 'import']);
    Route::get('/revert-sale-import/{batch}', [ImportSalesController::class, 'revertSaleImport']);

    Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', [SellPosController::class, 'getProductRow']);
    Route::post('/sells/pos/get_payment_row', [SellPosController::class, 'getPaymentRow']);
    Route::post('/sells/pos/get-reward-details', [SellPosController::class, 'getRewardDetails']);
    Route::get('/sells/pos/get-recent-transactions', [SellPosController::class, 'getRecentTransactions']);
    Route::get('/sells/pos/search-receipt', [SellPosController::class, 'searchReceipt']);
    Route::get('/sells/pos/get-product-suggestion', [SellPosController::class, 'getProductSuggestion']);
    Route::get('/sells/pos/get-featured-products/{location_id}', [SellPosController::class, 'getFeaturedProducts']);
    Route::get('/reset-mapping', [SellController::class, 'resetMapping']);
    // pos display screen route
    Route::get('/customer-display', [SellPosController::class, 'posDisplay'])->name('pos_display');
    Route::post('/sells/pos/toggle-favorite', [SellPosController::class, 'toggleFavorite']);
    Route::get('/sells/pos/get-customer-history', [SellPosController::class, 'getCustomerHistory']);
    Route::get('/contacts/check-credit-limit/{id}', [ContactController::class, 'checkCreditLimit']);

    Route::get('/pos/variations/bulk', [\App\Http\Controllers\ProductController::class, 'getVariationDetailsBulk']);
    // end pos display screen route

    Route::resource('roles', RoleController::class);

    Route::resource('users', ManageUserController::class);

    Route::resource('group-taxes', GroupTaxController::class);

    Route::get('/barcodes/set_default/{id}', [BarcodeController::class, 'setDefault']);
    Route::resource('barcodes', BarcodeController::class);

    //Invoice schemes..
    Route::get('/invoice-schemes/set_default/{id}', [InvoiceSchemeController::class, 'setDefault']);
    Route::resource('invoice-schemes', InvoiceSchemeController::class);

    //Print Labels
    Route::get('/labels/show', [LabelsController::class, 'show']);
    Route::get('/labels/add-product-row', [LabelsController::class, 'addProductRow']);
    Route::get('/labels/preview', [LabelsController::class, 'preview']);

    Route::get('/reports/gst-purchase-report', [ReportController::class, 'gstPurchaseReport']);
    Route::get('/reports/gst-sales-report', [ReportController::class, 'gstSalesReport']);
    Route::get('/reports/get-stock-by-sell-price', [ReportController::class, 'getStockBySellingPrice']);
    Route::get('/reports/purchase-report', [ReportController::class, 'purchaseReport']);
    Route::get('/reports/purchase-report-summary', [ReportController::class, 'getPurchaseReportSummary']);
    Route::get('/reports/sale-report', [ReportController::class, 'saleReport']);
    Route::get('/reports/sale-report-summary', [ReportController::class, 'getSaleReportSummary']);
    Route::get('/reports/service-staff-report', [ReportController::class, 'getServiceStaffReport']);
    Route::get('/reports/service-staff-line-orders', [ReportController::class, 'serviceStaffLineOrders']);
    Route::get('/reports/table-report', [ReportController::class, 'getTableReport']);
    Route::get('/reports/profit-loss', [ReportController::class, 'getProfitLoss']);
    Route::get('/reports/get-opening-stock', [ReportController::class, 'getOpeningStock']);
    Route::get('/reports/purchase-sell', [ReportController::class, 'getPurchaseSell']);
    Route::get('/reports/customer-supplier', [ReportController::class, 'getCustomerSuppliers']);
    
    // Separate Customer and Supplier Reports
    Route::get('/reports/customer-report', [ReportController::class, 'getCustomerReport'])->name('reports.customer');
    Route::get('/reports/supplier-report', [ReportController::class, 'getSupplierReport'])->name('reports.supplier');
    Route::get('/reports/followup-report', [ReportController::class, 'getFollowupReport'])->name('reports.followup');
    Route::get('/reports/orders-report', [ReportController::class, 'getOrdersReport'])->name('reports.orders');
    
    Route::get('/reports/stock-report', [ReportController::class, 'getStockReport']);
    Route::get('/reports/stock-details', [ReportController::class, 'getStockDetails']);
    Route::get('/reports/tax-report', [ReportController::class, 'getTaxReport']);
    Route::get('/reports/tax-details', [ReportController::class, 'getTaxDetails']);
    Route::get('/reports/trending-products', [ReportController::class, 'getTrendingProducts']);
    Route::get('/reports/expense-report', [ReportController::class, 'getExpenseReport']);
    Route::get('/reports/stock-adjustment-report', [ReportController::class, 'getStockAdjustmentReport']);
    Route::get('/reports/register-report', [ReportController::class, 'getRegisterReport']);
    Route::get('/reports/sales-representative-report', [ReportController::class, 'getSalesRepresentativeReport']);
    Route::get('/reports/sales-representative-total-expense', [ReportController::class, 'getSalesRepresentativeTotalExpense']);
    Route::get('/reports/sales-representative-total-sell', [ReportController::class, 'getSalesRepresentativeTotalSell']);
    Route::get('/reports/sales-representative-total-commission', [ReportController::class, 'getSalesRepresentativeTotalCommission']);
    Route::get('/reports/seller-daily-report', [ReportController::class, 'getSellerDailyReport']);
    Route::get('/reports/seller-daily-report-data', [ReportController::class, 'getSellerDailyReportData']);
    Route::get('/reports/stock-expiry', [ReportController::class, 'getStockExpiryReport']);
    Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', [ReportController::class, 'getStockExpiryReportEditModal']);
    Route::post('/reports/stock-expiry-update', [ReportController::class, 'updateStockExpiryReport'])->name('updateStockExpiryReport');
    Route::get('/reports/customer-group', [ReportController::class, 'getCustomerGroup']);
    Route::get('/reports/product-purchase-report', [ReportController::class, 'getproductPurchaseReport']);
    Route::get('/reports/product-sell-grouped-by', [ReportController::class, 'productSellReportBy']);
    Route::get('/reports/product-sell-report', [ReportController::class, 'getproductSellReport']);
    Route::get('/reports/product-sell-report-with-purchase', [ReportController::class, 'getproductSellReportWithPurchase']);
    Route::get('/reports/product-sell-grouped-report', [ReportController::class, 'getproductSellGroupedReport']);
    Route::get('/reports/lot-report', [ReportController::class, 'getLotReport']);
    Route::get('/reports/purchase-payment-report', [ReportController::class, 'purchasePaymentReport']);
    Route::get('/reports/sell-payment-report', [ReportController::class, 'sellPaymentReport']);
    Route::get('/reports/product-stock-details', [ReportController::class, 'productStockDetails']);
    Route::get('/reports/adjust-product-stock', [ReportController::class, 'adjustProductStock']);
    Route::get('/reports/get-profit/{by?}', [ReportController::class, 'getProfit']);
    Route::get('/reports/items-report', [ReportController::class, 'itemsReport']);
    Route::get('/reports/get-stock-value', [ReportController::class, 'getStockValue']);
    Route::get('/reports/dead-stock', [ReportController::class, 'getDeadStockReport']);
    Route::get('/home/morning-digest', [HomeController::class, 'getMorningDigest'])->name('home.morning_digest');
    Route::get('/reports/low-stock-velocity', [ReportController::class, 'getLowStockVelocityReport']);
    Route::get('/reports/customer-credit', [ReportController::class, 'getCustomerCreditReport']);
    Route::get('/reports/daily-summary', [ReportController::class, 'getDailySummaryReport'])->name('reports.daily_summary');
    Route::get('/reports/daily-summary-data', [ReportController::class, 'getDailySummaryData'])->name('reports.daily_summary_data');
    Route::get('/reports/daily-reconciliation', [ReportController::class, 'getDailyReconciliation'])->name('reports.daily_reconciliation');
    Route::get('/reports/purchase-price-variance', [ReportController::class, 'getPurchasePriceVarianceReport']);

    // Lost Sales
    Route::post('/lost-sales', [\App\Http\Controllers\LostSaleController::class, 'store'])->name('lost_sales.store');
    Route::get('/lost-sales/search', [\App\Http\Controllers\LostSaleController::class, 'search'])->name('lost_sales.search');
    Route::get('/reports/lost-sales', [\App\Http\Controllers\LostSaleController::class, 'index'])->name('reports.lost_sales');

    // POS Orders Routes
    Route::prefix('pos-customer-orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\OrderController::class, 'store'])->name('store');
        Route::get('/search/products', [\App\Http\Controllers\OrderController::class, 'searchProducts'])->name('searchProducts');
        Route::get('/pos/list', [\App\Http\Controllers\OrderController::class, 'getOrdersForPos'])->name('posOrders');
        Route::get('/{id}', [\App\Http\Controllers\OrderController::class, 'show'])->name('show');
        Route::get('/{id}/print', [\App\Http\Controllers\OrderController::class, 'printOrder'])->name('print');
        Route::get('/{id}/receipt', [\App\Http\Controllers\OrderController::class, 'getOrderReceipt'])->name('receipt');
        Route::post('/{id}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [\App\Http\Controllers\OrderController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/edit', [\App\Http\Controllers\OrderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\OrderController::class, 'update'])->name('update');
        Route::get('/{id}/fetch-details-json', [\App\Http\Controllers\OrderController::class, 'getOrderDetailsJson'])->name('fetchDetailsJson');
    });

    // Follow-ups Routes
    Route::prefix('pos-customer-followups')->name('followups.')->group(function () {
        Route::get('/', [\App\Http\Controllers\FollowupController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\FollowupController::class, 'store'])->name('store');
        Route::post('/{id}/status', [\App\Http\Controllers\FollowupController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [\App\Http\Controllers\FollowupController::class, 'destroy'])->name('destroy');
        Route::get('/search/products', [\App\Http\Controllers\FollowupController::class, 'searchProducts'])->name('searchProducts');
        Route::get('/pos/list', [\App\Http\Controllers\FollowupController::class, 'getFollowupsForPos'])->name('posFollowups');
        Route::get('/{id}/receipt', [\App\Http\Controllers\FollowupController::class, 'getFollowupReceipt'])->name('receipt');
    });

    Route::get('business-location/activate-deactivate/{location_id}', [BusinessLocationController::class, 'activateDeactivateLocation']);

    //Business Location Settings...
    Route::prefix('business-location/{location_id}')->name('location.')->group(function () {
        Route::get('settings', [LocationSettingsController::class, 'index'])->name('settings');
        Route::post('settings', [LocationSettingsController::class, 'updateSettings'])->name('settings_update');
    });

    //Business Locations...
    Route::post('business-location/check-location-id', [BusinessLocationController::class, 'checkLocationId']);
    Route::resource('business-location', BusinessLocationController::class);

    //Invoice layouts..
    Route::resource('invoice-layouts', InvoiceLayoutController::class);

    Route::post('get-expense-sub-categories', [ExpenseCategoryController::class, 'getSubCategories']);

    //Expense Categories...
    Route::resource('expense-categories', ExpenseCategoryController::class);

    //Expenses...
    Route::resource('expenses', ExpenseController::class);
    Route::get('import-expense', [ExpenseController::class, 'importExpense']);
    Route::post('store-import-expense', [ExpenseController::class, 'storeExpenseImport']);

    //Transaction payments...
    // Route::get('/payments/opening-balance/{contact_id}', 'TransactionPaymentController@getOpeningBalancePayments');
    Route::get('/payments/show-child-payments/{payment_id}', [TransactionPaymentController::class, 'showChildPayments']);
    Route::get('/payments/view-payment/{payment_id}', [TransactionPaymentController::class, 'viewPayment']);
    Route::get('/payments/add_payment/{transaction_id}', [TransactionPaymentController::class, 'addPayment']);
    Route::get('/payments/pay-contact-due/{contact_id}', [TransactionPaymentController::class, 'getPayContactDue']);
    Route::post('/payments/pay-contact-due', [TransactionPaymentController::class, 'postPayContactDue']);
    Route::resource('payments', TransactionPaymentController::class);

    //Printers...
    Route::resource('printers', PrinterController::class);

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', [StockAdjustmentController::class, 'removeExpiredStock']);
    Route::post('/stock-adjustments/get_product_row', [StockAdjustmentController::class, 'getProductRow']);
    Route::resource('stock-adjustments', StockAdjustmentController::class);

    // Stocktake (Physical Inventory Count)
    Route::get('/stocktake/products', [App\Http\Controllers\StocktakeController::class, 'getProducts'])->name('stocktake.products');
    Route::get('/stocktake/search-products', [App\Http\Controllers\StocktakeController::class, 'searchProducts'])->name('stocktake.searchProducts');
    Route::post('/stocktake/{id}/save-counts', [App\Http\Controllers\StocktakeController::class, 'saveCounts'])->name('stocktake.saveCounts');
    Route::get('/stocktake/{id}/complete', [App\Http\Controllers\StocktakeController::class, 'complete'])->name('stocktake.complete');
    Route::get('/stocktake/{id}/variance-report', [App\Http\Controllers\StocktakeController::class, 'varianceReport'])->name('stocktake.varianceReport');
    Route::get('/stocktake/{id}/print-count-sheet', [App\Http\Controllers\StocktakeController::class, 'printCountSheet'])->name('stocktake.printCountSheet');
    Route::get('/stocktake/{id}/print-verification', [App\Http\Controllers\StocktakeController::class, 'printVerificationSheet'])->name('stocktake.printVerificationSheet');
    Route::resource('stocktake', App\Http\Controllers\StocktakeController::class);

    Route::get('/cash-register/register-details', [CashRegisterController::class, 'getRegisterDetails']);
    Route::get('/cash-register/close-register/{id?}', [CashRegisterController::class, 'getCloseRegister']);
    Route::post('/cash-register/close-register', [CashRegisterController::class, 'postCloseRegister']);
    Route::resource('cash-register', CashRegisterController::class);

    //Import products
    Route::get('/import-products', [ImportProductsController::class, 'index']);
    Route::post('/import-products/store', [ImportProductsController::class, 'store']);
    Route::get('/import-products/download-template', [ImportProductsController::class, 'downloadTemplate']);

    //Sales Commission Agent
    Route::resource('sales-commission-agents', SalesCommissionAgentController::class);

    //Stock Transfer
    Route::get('stock-transfers/print/{id}', [StockTransferController::class, 'printInvoice']);
    Route::post('stock-transfers/update-status/{id}', [StockTransferController::class, 'updateStatus']);
    Route::resource('stock-transfers', StockTransferController::class);

    Route::get('/opening-stock/add/{product_id}', [OpeningStockController::class, 'add']);
    Route::post('/opening-stock/save', [OpeningStockController::class, 'save']);

    //Customer Groups
    Route::resource('customer-group', CustomerGroupController::class);

    //Import opening stock
    Route::get('/import-opening-stock', [ImportOpeningStockController::class, 'index']);
    Route::post('/import-opening-stock/store', [ImportOpeningStockController::class, 'store']);

    //Sell return
    Route::get('validate-invoice-to-return/{invoice_no}', [SellReturnController::class, 'validateInvoiceToReturn']);
    // service staff replacement
    Route::get('validate-invoice-to-service-staff-replacement/{invoice_no}', [SellPosController::class, 'validateInvoiceToServiceStaffReplacement']);
    Route::put('change-service-staff/{id}', [SellPosController::class, 'change_service_staff'])->name('change_service_staff');

    Route::resource('sell-return', SellReturnController::class);
    Route::get('sell-return/get-product-row', [SellReturnController::class, 'getProductRow']);
    Route::get('/sell-return/print/{id}', [SellReturnController::class, 'printInvoice']);
    Route::get('/sell-return/add/{id}', [SellReturnController::class, 'add']);

    //Backup
    Route::get('backup/download/{file_name}', [BackUpController::class, 'download']);
    Route::get('backup/{id}/delete', [BackUpController::class, 'delete'])->name('delete_backup');
    Route::resource('backup', BackUpController::class)->only('index', 'create', 'store');

    Route::get('selling-price-group/activate-deactivate/{id}', [SellingPriceGroupController::class, 'activateDeactivate']);
    Route::get('update-product-price', [SellingPriceGroupController::class, 'updateProductPrice'])->name('update-product-price');
    Route::get('export-product-price', [SellingPriceGroupController::class, 'export']);
    Route::post('import-product-price', [SellingPriceGroupController::class, 'import']);

    Route::resource('selling-price-group', SellingPriceGroupController::class);

    Route::resource('notification-templates', NotificationTemplateController::class)->only(['index', 'store']);
    Route::get('notification/get-template/{transaction_id}/{template_for}', [NotificationController::class, 'getTemplate']);
    Route::post('notification/send', [NotificationController::class, 'send']);

    Route::post('/purchase-return/update', [CombinedPurchaseReturnController::class, 'update']);
    Route::get('/purchase-return/edit/{id}', [CombinedPurchaseReturnController::class, 'edit']);
    Route::post('/purchase-return/save', [CombinedPurchaseReturnController::class, 'save']);
    Route::post('/purchase-return/get_product_row', [CombinedPurchaseReturnController::class, 'getProductRow']);
    Route::get('/purchase-return/create', [CombinedPurchaseReturnController::class, 'create']);
    Route::get('/purchase-return/add/{id}', [PurchaseReturnController::class, 'add']);
    Route::resource('/purchase-return', PurchaseReturnController::class)->except('create');

    Route::get('/discount/activate/{id}', [DiscountController::class, 'activate']);
    Route::post('/discount/mass-deactivate', [DiscountController::class, 'massDeactivate']);
    Route::resource('discount', DiscountController::class);

    Route::prefix('account')->group(function () {
        Route::resource('/account', AccountController::class);
        Route::get('/fund-transfer/{id}', [AccountController::class, 'getFundTransfer']);
        Route::post('/fund-transfer', [AccountController::class, 'postFundTransfer']);
        Route::get('/deposit/{id}', [AccountController::class, 'getDeposit']);
        Route::post('/deposit', [AccountController::class, 'postDeposit']);
        Route::get('/close/{id}', [AccountController::class, 'close']);
        Route::get('/activate/{id}', [AccountController::class, 'activate']);
        Route::get('/delete-account-transaction/{id}', [AccountController::class, 'destroyAccountTransaction']);
        Route::get('/edit-account-transaction/{id}', [AccountController::class, 'editAccountTransaction']);
        Route::post('/update-account-transaction/{id}', [AccountController::class, 'updateAccountTransaction']);
        Route::get('/get-account-balance/{id}', [AccountController::class, 'getAccountBalance']);
        Route::get('/balance-sheet', [AccountReportsController::class, 'balanceSheet']);
        Route::get('/trial-balance', [AccountReportsController::class, 'trialBalance']);
        Route::get('/payment-account-report', [AccountReportsController::class, 'paymentAccountReport']);
        Route::get('/link-account/{id}', [AccountReportsController::class, 'getLinkAccount']);
        Route::post('/link-account', [AccountReportsController::class, 'postLinkAccount']);
        Route::get('/cash-flow', [AccountController::class, 'cashFlow']);
    });

    Route::resource('account-types', AccountTypeController::class);

    //Restaurant module
    Route::prefix('modules')->group(function () {
        Route::resource('tables', Restaurant\TableController::class);
        Route::resource('modifiers', Restaurant\ModifierSetsController::class);

        //Map modifier to products
        Route::get('/product-modifiers/{id}/edit', [Restaurant\ProductModifierSetController::class, 'edit']);
        Route::post('/product-modifiers/{id}/update', [Restaurant\ProductModifierSetController::class, 'update']);
        Route::get('/product-modifiers/product-row/{product_id}', [Restaurant\ProductModifierSetController::class, 'product_row']);

        Route::get('/add-selected-modifiers', [Restaurant\ProductModifierSetController::class, 'add_selected_modifiers']);

        Route::get('/kitchen', [Restaurant\KitchenController::class, 'index']);
        Route::get('/kitchen/mark-as-cooked/{id}', [Restaurant\KitchenController::class, 'markAsCooked']);
        Route::post('/refresh-orders-list', [Restaurant\KitchenController::class, 'refreshOrdersList']);
        Route::post('/refresh-line-orders-list', [Restaurant\KitchenController::class, 'refreshLineOrdersList']);

        Route::get('/orders', [Restaurant\OrderController::class, 'index']);
        Route::get('/orders/mark-as-served/{id}', [Restaurant\OrderController::class, 'markAsServed']);
        Route::get('/data/get-pos-details', [Restaurant\DataController::class, 'getPosDetails']);
        Route::get('/data/check-staff-pin', [Restaurant\DataController::class, 'checkStaffPin']);
        Route::get('/orders/mark-line-order-as-served/{id}', [Restaurant\OrderController::class, 'markLineOrderAsServed']);
        Route::get('/print-line-order', [Restaurant\OrderController::class, 'printLineOrder']);
    });

    Route::get('bookings/get-todays-bookings', [Restaurant\BookingController::class, 'getTodaysBookings']);
    Route::resource('bookings', Restaurant\BookingController::class);

    Route::resource('types-of-service', TypesOfServiceController::class);
    Route::get('sells/edit-shipping/{id}', [SellController::class, 'editShipping']);
    Route::put('sells/update-shipping/{id}', [SellController::class, 'updateShipping']);
    Route::get('shipments', [SellController::class, 'shipments']);

    Route::post('upload-module', [Install\ModulesController::class, 'uploadModule']);
    Route::delete('manage-modules/destroy/{module_name}', [Install\ModulesController::class, 'destroy']);
    Route::resource('manage-modules', Install\ModulesController::class)
        ->only(['index', 'update']);
    Route::get('regenerate', [Install\ModulesController::class, 'regenerate']);

    Route::resource('warranties', WarrantyController::class);

    Route::resource('dashboard-configurator', DashboardConfiguratorController::class)
    ->only(['edit', 'update']);

    Route::get('view-media/{model_id}', [SellController::class, 'viewMedia']);

    //common controller for document & note
    Route::get('get-document-note-page', [DocumentAndNoteController::class, 'getDocAndNoteIndexPage']);
    Route::post('post-document-upload', [DocumentAndNoteController::class, 'postMedia']);
    Route::resource('note-documents', DocumentAndNoteController::class);
    Route::resource('purchase-order', PurchaseOrderController::class);
    Route::get('get-purchase-orders/{contact_id}', [PurchaseOrderController::class, 'getPurchaseOrders']);
    Route::get('get-purchase-order-lines/{purchase_order_id}', [PurchaseController::class, 'getPurchaseOrderLines']);
    Route::get('edit-purchase-orders/{id}/status', [PurchaseOrderController::class, 'getEditPurchaseOrderStatus']);
    Route::put('update-purchase-orders/{id}/status', [PurchaseOrderController::class, 'postEditPurchaseOrderStatus']);
    Route::resource('sales-order', SalesOrderController::class)->only(['index']);
    Route::get('get-sales-orders/{customer_id}', [SalesOrderController::class, 'getSalesOrders']);
    Route::get('get-sales-order-lines', [SellPosController::class, 'getSalesOrderLines']);
    Route::get('edit-sales-orders/{id}/status', [SalesOrderController::class, 'getEditSalesOrderStatus']);
    Route::put('update-sales-orders/{id}/status', [SalesOrderController::class, 'postEditSalesOrderStatus']);
    Route::get('reports/daily-product-profit', [ReportController::class, 'getDailyProductProfitReport']);
    Route::get('reports/activity-log', [ReportController::class, 'activityLog']);
    Route::get('user-location/{latlng}', [HomeController::class, 'getUserLocation']);

    // ── Hospital Billing ─────────────────────────────────────────────────────
    Route::get('hospital-billing/{id}/print', [HospitalBillingController::class, 'printBill'])->name('hospital-billing.print');
    Route::resource('hospital-billing', HospitalBillingController::class);

    // ── Parcel Management ─────────────────────────────────────────────────────
    Route::post('parcels/{id}/status', [ParcelController::class, 'updateStatus'])->name('parcels.update-status');
    Route::post('parcels/bulk-scan', [ParcelController::class, 'bulkScan'])->name('parcels.bulk-scan');
    Route::get('parcels/{id}/waybill', [ParcelController::class, 'waybill'])->name('parcels.waybill');
    Route::get('parcel-manifest', [ParcelController::class, 'manifest'])->name('parcel-manifest');
    Route::get('parcel-track', [ParcelController::class, 'track'])->name('parcels.track');
    Route::get('parcel-reports', [ParcelController::class, 'reports'])->name('parcel-reports');
    Route::resource('parcels', ParcelController::class);

    // Parcel Routes CRUD + price calculator
    Route::get('parcel-routes/calculate-price', [ParcelRouteController::class, 'calculatePrice'])->name('parcel-routes.calculate-price');
    Route::resource('parcel-routes', ParcelRouteController::class);

    // ── SaaS Admin ───────────────────────────────────────────────────────────
    Route::post('saas-admin/{business_id}/features', [SaasAdminController::class, 'updateBusinessFeatures'])->name('saas-admin.update-features');
    Route::get('saas-admin/{business_id}/features', [SaasAdminController::class, 'businessFeatures'])->name('saas-admin.features');
    Route::get('saas-admin', [SaasAdminController::class, 'businessIndex'])->name('saas-admin.index');

    // M-Pesa Routes
    Route::prefix('mpesa')->name('mpesa.')->group(function () {
        Route::get('/settings', [MpesaController::class, 'settings'])->name('settings');
        Route::post('/settings', [MpesaController::class, 'saveSettings'])->name('settings.save');
        Route::post('/test-connection', [MpesaController::class, 'testConnection'])->name('test');
        Route::post('/stk-push', [MpesaController::class, 'stkPush'])->name('stk-push');
        Route::post('/query-status', [MpesaController::class, 'queryStatus'])->name('query-status');
        Route::post('/register-urls', [MpesaController::class, 'registerUrls'])->name('register-urls');
        Route::get('/c2b-payments', [MpesaController::class, 'c2bPayments'])->name('c2b-payments');
        Route::post('/match-payment', [MpesaController::class, 'matchPayment'])->name('match-payment');
        Route::get('/transactions', [MpesaController::class, 'transactions'])->name('transactions');
        Route::get('/daily-summary', [MpesaController::class, 'dailySummary'])->name('daily-summary');
        Route::post('/check-payment-status', [MpesaController::class, 'checkPaymentStatus'])->name('check-status');
        Route::post('/check-balance', [MpesaController::class, 'checkBalance'])->name('check-balance');
        Route::post('/pay-expense', [MpesaController::class, 'payExpense'])->name('pay-expense');
        Route::get('/payment-modal', [MpesaController::class, 'getPaymentModal'])->name('payment-modal');
        Route::post('/initiate-payment', [MpesaController::class, 'initiatePayment'])->name('initiate-payment');
        Route::get('/get-unassigned-payments', [MpesaController::class, 'getUnassignedPayments'])->name('get-unassigned-payments');
        Route::get('/check-matching-payment', [MpesaController::class, 'checkMatchingPayment'])->name('check-matching-payment-payment');
    });

    // Pesapal Gateway Routes (per-business credentials, v3 API)
    Route::prefix('pesapal')->name('pesapal.')->group(function () {
        Route::get('/settings',              [\App\Http\Controllers\PesapalGatewayController::class, 'settings'])->name('settings');
        Route::post('/settings',             [\App\Http\Controllers\PesapalGatewayController::class, 'saveSettings'])->name('settings.save');
        Route::post('/test-connection',      [\App\Http\Controllers\PesapalGatewayController::class, 'testConnection'])->name('test');
        Route::post('/register-ipn',         [\App\Http\Controllers\PesapalGatewayController::class, 'registerIpn'])->name('register-ipn');
        Route::post('/initiate-payment',     [\App\Http\Controllers\PesapalGatewayController::class, 'initiatePayment'])->name('initiate-payment');
        Route::post('/check-payment-status', [\App\Http\Controllers\PesapalGatewayController::class, 'checkPaymentStatus'])->name('check-status');
        Route::get('/transactions',          [\App\Http\Controllers\PesapalGatewayController::class, 'transactions'])->name('transactions');
        Route::get('/daily-summary',         [\App\Http\Controllers\PesapalGatewayController::class, 'dailySummary'])->name('daily-summary');
    });

    // Cloud Sync Dashboard (full auth + sidebar)
    Route::get('/sync',               [CloudSyncController::class, 'dashboard'])->name('sync.dashboard');
    Route::delete('/sync/token/{id}', [CloudSyncController::class, 'revokeToken'])->name('sync.token.revoke');

    // ── Per-Business Feature Management ──────────────────────────────────────
    Route::get('/business-features',             [\App\Http\Controllers\BusinessFeaturesController::class, 'index'])->name('business-features.index');
    Route::post('/business-features/toggle',     [\App\Http\Controllers\BusinessFeaturesController::class, 'toggle'])->name('business-features.toggle');
    Route::post('/business-features/enable-all', [\App\Http\Controllers\BusinessFeaturesController::class, 'enableAll'])->name('business-features.enable-all');
    Route::post('/business-features/disable-all',[\App\Http\Controllers\BusinessFeaturesController::class, 'disableAll'])->name('business-features.disable-all');

    // ── Customer Order Token Management (owner/authenticated) ──────────────
    Route::get('/customer-order-links', [CustomerOrderController::class, 'index'])->name('customer_order.links');
    Route::get('/contacts/{contact_id}/order-token', [CustomerOrderController::class, 'generateToken'])->name('customer_order.generate_token');
    Route::post('/contacts/{contact_id}/regenerate-order-token', [CustomerOrderController::class, 'regenerateToken'])->name('customer_order.regenerate_token');
});

// ── Customer Order Links (public, no auth required) ─────────────────────────
// Fixed routes must be declared BEFORE the {token} wildcard routes
Route::post('/customer-order/check-payment', [CustomerOrderController::class, 'checkPayment'])->name('customer_order.check_payment');
Route::get('/customer-order/{token}/search', [CustomerOrderController::class, 'search'])->name('customer_order.search');
Route::get('/customer-order/{token}', [CustomerOrderController::class, 'show'])->name('customer_order.show');
Route::post('/customer-order/{token}', [CustomerOrderController::class, 'store'])->name('customer_order.store');

// Pesapal Webhooks (public, no auth — called by Pesapal)
Route::get('/pesapal/ipn',      [\App\Http\Controllers\PesapalGatewayController::class, 'ipnCallback'])->name('pesapal.ipn');
Route::get('/pesapal/callback', [\App\Http\Controllers\PesapalGatewayController::class, 'paymentCallback'])->name('pesapal.customer-callback');

// M-Pesa Webhooks (public, no auth required - called by Safaricom)
// NOTE: Using 'mobile-money' instead of 'mpesa' because Safaricom rejects URLs containing 'MPESA'
Route::prefix('mobile-money/webhook')->group(function () {
    Route::post('/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');
    Route::post('/validation', [MpesaController::class, 'validation'])->name('mpesa.validation');
    Route::post('/confirmation', [MpesaController::class, 'confirmation'])->name('mpesa.confirmation');
});

// Route::middleware(['EcomApi'])->prefix('api/ecom')->group(function () {
//     Route::get('products/{id?}', [ProductController::class, 'getProductsApi']);
//     Route::get('categories', [CategoryController::class, 'getCategoriesApi']);
//     Route::get('brands', [BrandController::class, 'getBrandsApi']);
//     Route::post('customers', [ContactController::class, 'postCustomersApi']);
//     Route::get('settings', [BusinessController::class, 'getEcomSettings']);
//     Route::get('variations', [ProductController::class, 'getVariationsApi']);
//     Route::post('orders', [SellPosController::class, 'placeOrdersApi']);
// });

//common route
// Route::middleware(['auth'])->group(function () {
//     Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
// });

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone'])->group(function () {
    Route::get('/load-more-notifications', [HomeController::class, 'loadMoreNotifications']);
    Route::get('/get-total-unread', [HomeController::class, 'getTotalUnreadNotifications']);
    Route::get('/purchases/print/{id}', [PurchaseController::class, 'printInvoice']);
    Route::get('/purchases/{id}', [PurchaseController::class, 'show']);
    Route::get('/download-purchase-order/{id}/pdf', [PurchaseOrderController::class, 'downloadPdf'])->name('purchaseOrder.downloadPdf');
    Route::get('/sells/{id}', [SellController::class, 'show']);
    Route::get('/sells/{transaction_id}/print', [SellPosController::class, 'printInvoice'])->name('sell.printInvoice');
    Route::get('/download-sells/{transaction_id}/pdf', [SellPosController::class, 'downloadPdf'])->name('sell.downloadPdf');
    Route::get('/download-quotation/{id}/pdf', [SellPosController::class, 'downloadQuotationPdf'])
        ->name('quotation.downloadPdf');
    Route::get('/download-packing-list/{id}/pdf', [SellPosController::class, 'downloadPackingListPdf'])
        ->name('packing.downloadPdf');
    Route::get('/sells/invoice-url/{id}', [SellPosController::class, 'showInvoiceUrl']);
    Route::get('/show-notification/{id}', [HomeController::class, 'showNotification']);
    Route::post('/sell/check-invoice-number', [SellController::class, 'checkInvoiceNumber']);
});