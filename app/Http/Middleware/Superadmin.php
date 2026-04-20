<?php

namespace App\Http\Middleware;

use Closure;

class Superadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (empty($user)) {
            abort(403, 'Unauthorized action.');
        }

        // Check 1: Spatie role (preferred — set via artisan saas:create-admin)
        if ($user->hasRole('Superadmin')) {
            return $next($request);
        }

        // Check 2: ADMINISTRATOR_USERNAMES env fallback
        $administrator_list = config('constants.administrator_usernames');
        if (! empty($administrator_list) &&
            in_array(strtolower($user->username), explode(',', strtolower($administrator_list)))) {
            return $next($request);
        }

        // Check 3: dedicated saas_admin account (created via artisan saas:create-admin)
        if ($user->username === 'saas_admin') {
            return $next($request);
        }

        // Check 4: known platform admin email (matches LoginController redirect logic)
        $saasAdminEmail = env('SAAS_ADMIN_EMAIL', 'admin@apexpos.co.ke');
        if (! empty($saasAdminEmail) && strtolower($user->email) === strtolower($saasAdminEmail)) {
            return $next($request);
        }

        // Check 5: no business_id = pure platform admin (superadmin with no business)
        if (empty($user->business_id)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action. Your account does not have Superadmin access.');
    }
}
