<?php

namespace App\Http\Middleware;

use Closure;
use App\SaasSubscription;

/**
 * Blocks access when the most recent SaaS subscription is past its
 * ends_at AND grace_ends_at. Businesses with no subscription row
 * (legacy installs) are allowed through.
 *
 * Whitelisted paths: /saas/portal, /saas/mpesa/*, /logout — so the
 * user can still pay/renew or sign out.
 */
class TrialExpired
{
    protected array $whitelistPrefixes = [
        'saas/',
        'logout',
        'login',
    ];

    public function handle($request, Closure $next)
    {
        foreach ($this->whitelistPrefixes as $prefix) {
            if ($request->is($prefix . '*') || $request->is($prefix)) {
                return $next($request);
            }
        }

        $businessId = session('business.id') ?? optional(auth()->user())->business_id;
        if (!$businessId) {
            return $next($request);
        }

        $sub = SaasSubscription::where('business_id', $businessId)->latest()->first();
        if (!$sub) {
            return $next($request); // pre-SaaS business
        }

        // Auto-transition: trial → expired, active → grace → expired
        $this->reconcileStatus($sub);

        if (!$sub->isAccessible()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'Subscription expired',
                    'renewAt' => route('saas.portal'),
                ], 402);
            }
            return redirect()->route('saas.portal')->with('error',
                'Your subscription or trial has expired. Please activate a plan to continue.');
        }

        return $next($request);
    }

    protected function reconcileStatus(SaasSubscription $sub): void
    {
        $now = now();
        $dirty = false;

        if ($sub->status === 'trial' && $sub->ends_at && $sub->ends_at->isPast()) {
            if ($sub->grace_ends_at && $sub->grace_ends_at->isFuture()) {
                $sub->status = 'grace';
            } else {
                $sub->status = 'expired';
            }
            $dirty = true;
        }

        if ($sub->status === 'active' && $sub->ends_at && $sub->ends_at->isPast()) {
            $sub->status = ($sub->grace_ends_at && $sub->grace_ends_at->isFuture()) ? 'grace' : 'expired';
            $dirty = true;
        }

        if ($sub->status === 'grace' && $sub->grace_ends_at && $sub->grace_ends_at->isPast()) {
            $sub->status = 'expired';
            $dirty = true;
        }

        if ($dirty) {
            $sub->save();
        }
    }
}
