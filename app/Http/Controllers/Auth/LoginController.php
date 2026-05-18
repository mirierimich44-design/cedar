<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Rules\ReCaptcha;
use App\Models\IpAllowed;
use App\Models\IpAccessLog;
use App\Models\UserIpSetting;
use App\Models\AccessSchedule;
use App\Services\IpLookupService;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * All Utils instance.
     */
    protected $businessUtil;

    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    public function showLoginForm()
    {
        // Detect business by subdomain and load its login branding
        $loginSettings = [];
        try {
            $host      = request()->getHost();
            $parts     = explode('.', $host);
            $subdomain = count($parts) >= 3 ? $parts[0] : null;

            $business = null;
            if ($subdomain) {
                $business = \App\Business::whereRaw('LOWER(name) = ?', [strtolower($subdomain)])->first();
            }
            if (! $business) {
                $business = \App\Business::where('is_active', 1)->first();
            }
            if ($business && ! empty($business->login_settings)) {
                $loginSettings = json_decode($business->login_settings, true) ?? [];
            }
        } catch (\Throwable $e) {
            // fail silently — defaults will be used
        }

        // Allow registration: check system table, fall back to env
        $allowRegistration = \App\System::getProperty('allow_registration');
        if ($allowRegistration === null) {
            $allowRegistration = config('constants.allow_registration');
        } else {
            $allowRegistration = (bool) $allowRegistration;
        }

        $username = '';
        $password = '';
        if (config('app.env') == 'demo' && request('demo_type')) {
            // preserve demo login passthrough if needed
        }

        return view('auth.login', compact('loginSettings', 'allowRegistration', 'username', 'password'));
    }

    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }

    public function logout()
    {
        $this->businessUtil->activityLog(auth()->user(), 'logout');

        request()->session()->flush();
        \Auth::logout();

        return redirect('/login');
    }

    /**
     * The user has been authenticated.
     * Check if the business is active or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // ── Superadmin — always let through, no restrictions ──────────────
        $isSuperadmin = $user->username === 'saas_admin'
            || $user->email === 'admin@apexpos.co.ke'
            || $user->hasRole('Superadmin');

        if ($isSuperadmin) {
            $this->logAttempt($request, $user, 'success');
            return redirect('/saas-admin');
        }

        // ── Standard account checks ───────────────────────────────────────
        if (! $user->business || ! $user->business->is_active) {
            \Auth::logout();

            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
              );
        } elseif ($user->status != 'active') {
            \Auth::logout();

            return redirect('/login')
              ->with(
                  'status',
                  ['success' => 0, 'msg' => __('lang_v1.user_inactive')]
              );
        } elseif (! $user->allow_login) {
            \Auth::logout();

            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]
                );
        } elseif (($user->user_type == 'user_customer') && ! $this->moduleUtil->hasThePermissionInSubscription($user->business_id, 'crm_module')) {
            \Auth::logout();

            return redirect('/login')
                ->with(
                    'status',
                    ['success' => 0, 'msg' => __('lang_v1.business_dont_have_crm_subscription')]
                );
        }

        $clientIp   = $request->ip();
        $businessId = $user->business_id;

        // ── Banned IP check — always enforced regardless of restriction setting ──
        if (IpAllowed::isBanned($clientIp, $businessId)) {
            $this->logAttempt($request, $user, 'blocked_ip');
            \Auth::logout();
            return redirect('/login')->with('status', [
                'success' => 0,
                'msg'     => 'Access denied: your IP address (' . $clientIp . ') has been banned. Contact your administrator.',
            ]);
        }

        // ── IP Whitelist check — only when restriction is enabled ─────────────
        // Business owner (Admin role) is always exempt — can never be locked out.
        if (! empty($user->business->enable_ip_restriction)) {

            $isOwner = ($user->id === $user->business->owner_id);

            $userSetting = UserIpSetting::where('user_id', $user->id)->first();
            $hasBypass   = $userSetting && $userSetting->bypass_ip_check;

            if (! $isOwner && ! $hasBypass) {
                if (! IpAllowed::isWhitelisted($clientIp, $businessId)) {
                    $this->logAttempt($request, $user, 'blocked_ip');
                    \Auth::logout();
                    return redirect('/login')->with('status', [
                        'success' => 0,
                        'msg'     => 'Access denied: your location (' . $clientIp . ') is not on the approved network list. Contact your administrator.',
                    ]);
                }
            }
        }

        // ── All checks passed — log success ──────────────────────────────────
        $this->logAttempt($request, $user, 'success');
    }

    protected function redirectTo()
    {
        $user = \Auth::user();

        if ($user->username === 'saas_admin' || $user->email === 'admin@apexpos.co.ke') {
            return '/saas-admin';
        }

        if (! $user->can('dashboard.data') && $user->can('sell.create')) {
            return action([\App\Http\Controllers\SellPosController::class, 'create']);
        }

        if ($user->user_type == 'user_customer') {
            return 'contact/contact-dashboard';
        }

        return '/home';
    }

    public function validateLogin(Request $request)
    {
        if(config('constants.enable_recaptcha')){
            $this->validate($request, [
                $this->username() => 'required|string',
                'password' => 'required|string',
                'g-recaptcha-response' => ['required', new ReCaptcha]
            ]);
        }else{
            $this->validate($request, [
                $this->username() => 'required|string',
                'password' => 'required|string',
            ]);
        }
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $this->logAttempt($request, null, 'wrong_password');

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([$this->username() => trans('auth.failed')]);
    }

    private function logAttempt(Request $request, $user, string $outcome): void
    {
        try {
            $lookup = app(IpLookupService::class)->lookup($request->ip());

            // Resolve the user's primary location (first permitted location)
            $locationId = null;
            if ($user && $user->business_id) {
                $permitted = $user->permitted_locations($user->business_id);
                if (is_array($permitted) && count($permitted)) {
                    $locationId = $permitted[0];
                }
            }

            IpAccessLog::create([
                'user_id'              => $user?->id,
                'business_id'          => $user?->business_id,
                'business_location_id' => $locationId,
                'username_attempted'   => $request->input($this->username()),
                'ip_address'           => $request->ip(),
                'isp'                  => $lookup['isp'] ?? null,
                'country'              => $lookup['country'] ?? null,
                'city'                 => $lookup['city'] ?? null,
                'browser'              => $lookup['browser'] ?? null,
                'os'                   => $lookup['os'] ?? null,
                'device_type'          => $lookup['device_type'] ?? null,
                'outcome'              => $outcome,
            ]);
        } catch (\Throwable $e) {
            // never break login because of logging failure
        }
    }

}
