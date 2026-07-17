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

        // Prefer full branded login; fall back if views missing after partial deploy
        if (view()->exists('auth.login')) {
            try {
                return view('auth.login', compact('loginSettings', 'allowRegistration', 'username', 'password'));
            } catch (\Throwable $e) {
                \Log::warning('auth.login view failed: '.$e->getMessage());
            }
        }

        return $this->emergencyLoginForm($username);
    }

    /**
     * Minimal login page with zero Blade layout dependencies.
     * Used when resources/views/auth/login.blade.php is missing on the server.
     */
    protected function emergencyLoginForm($username = '')
    {
        $action = url('/login');
        $csrf = csrf_token();
        $user = e(old('username', $username));
        $error = session('error') ?: (session('status.msg') ?? '');
        $errorHtml = $error ? '<p style="color:#b91c1c;margin:0 0 12px">'.e($error).'</p>' : '';
        $errors = session('errors');
        if ($errors && method_exists($errors, 'any') && $errors->any()) {
            $errorHtml .= '<p style="color:#b91c1c;margin:0 0 12px">'.e($errors->first()).'</p>';
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{$csrf}">
<title>Login — Apex POS</title>
<style>
body{font-family:system-ui,sans-serif;background:#0f766e;margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center}
.box{background:#fff;border-radius:14px;padding:28px 24px;width:100%;max-width:380px;box-shadow:0 10px 40px rgba(0,0,0,.2)}
h1{margin:0 0 6px;font-size:20px;color:#0f172a}
p.sub{margin:0 0 18px;font-size:13px;color:#64748b}
label{display:block;font-size:12px;font-weight:700;margin:0 0 4px;color:#334155}
input{width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;margin-bottom:12px;font-size:14px}
button{width:100%;padding:12px;border:0;border-radius:8px;background:#0f766e;color:#fff;font-weight:700;font-size:14px;cursor:pointer}
button:hover{background:#0d9488}
.note{margin-top:14px;font-size:11px;color:#94a3b8;line-height:1.4}
</style>
</head>
<body>
<div class="box">
  <h1>Apex POS Login</h1>
  <p class="sub">Emergency login (auth view missing on server — upload auth views after login)</p>
  {$errorHtml}
  <form method="post" action="{$action}">
    <input type="hidden" name="_token" value="{$csrf}">
    <label>Username</label>
    <input type="text" name="username" value="{$user}" required autofocus autocomplete="username">
    <label>Password</label>
    <input type="password" name="password" required autocomplete="current-password">
    <label style="font-weight:500;margin-bottom:12px"><input type="checkbox" name="remember" value="1" style="width:auto"> Remember me</label>
    <button type="submit">Sign in</button>
  </form>
  <p class="note">After login works, upload:<br>
  resources/views/auth/login.blade.php<br>
  resources/views/layouts/auth2.blade.php</p>
</div>
</body>
</html>
HTML;

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
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
                'msg'     => 'You cannot log in at this time. Please try again later.',
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
                        'msg'     => 'You cannot log in at this time. Please try again later.',
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
            // Parse User-Agent inline (avoids jenssegers/agent composer dependency,
            // which isn't installed on every environment).
            $ua         = $request->userAgent() ?? '';
            $deviceType = $this->parseDeviceType($ua);
            $browser    = $this->parseBrowser($ua);
            $os         = $this->parseOs($ua);

            // Resolve business_id — use authenticated user if available,
            // otherwise look up by username so failed/blocked attempts are still logged.
            $businessId = $user?->business_id;
            if (! $businessId) {
                $attempted = \App\User::where('username', $request->input($this->username()))->first();
                $businessId = $attempted?->business_id;
            }

            // Resolve the user's primary location (first permitted location).
            $locationId = null;
            if ($user && $user->business_id) {
                try {
                    $permitted = $user->permitted_locations($user->business_id);
                    if (is_array($permitted) && count($permitted)) {
                        $locationId = $permitted[0];
                    }
                } catch (\Throwable $e) {
                    // permitted_locations() missing or errored — non-fatal
                }
            }

            // Write the log immediately (no geo yet — keeps login fast).
            $log = IpAccessLog::create([
                'user_id'              => $user?->id,
                'business_id'          => $businessId,
                'business_location_id' => $locationId,
                'username_attempted'   => $request->input($this->username()),
                'ip_address'           => $request->ip(),
                'browser'              => $browser,
                'os'                   => $os,
                'device_type'          => $deviceType,
                'outcome'              => $outcome,
            ]);

            // Geo lookup AFTER the HTTP response is sent — never blocks the login.
            // dispatchAfterResponse() runs the job as a terminating callback,
            // bypassing QUEUE_CONNECTION=sync (which would otherwise run inline).
            \App\Jobs\LookupGeoForLog::dispatchAfterResponse($log->id, $request->ip());

        } catch (\Throwable $e) {
            \Log::error('IpAccessLog failed: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    private function parseDeviceType(string $ua): string
    {
        if (preg_match('/iPad|Tablet|Kindle|Silk/i', $ua))         return 'tablet';
        if (preg_match('/Mobile|Android|iPhone|iPod|Opera Mini/i', $ua)) return 'mobile';
        return 'desktop';
    }

    private function parseBrowser(string $ua): ?string
    {
        if (preg_match('/Edg\//i', $ua))                return 'Edge';
        if (preg_match('/OPR\/|Opera/i', $ua))          return 'Opera';
        if (preg_match('/Firefox\//i', $ua))            return 'Firefox';
        if (preg_match('/Chrome\//i', $ua))             return 'Chrome';
        if (preg_match('/Safari\//i', $ua))             return 'Safari';
        if (preg_match('/MSIE|Trident/i', $ua))         return 'IE';
        return null;
    }

    private function parseOs(string $ua): ?string
    {
        if (preg_match('/Windows NT/i', $ua))           return 'Windows';
        if (preg_match('/Android/i', $ua))              return 'Android';
        if (preg_match('/iPhone|iPad|iPod|iOS/i', $ua)) return 'iOS';
        if (preg_match('/Mac OS X|Macintosh/i', $ua))   return 'macOS';
        if (preg_match('/Linux/i', $ua))                return 'Linux';
        return null;
    }

}
