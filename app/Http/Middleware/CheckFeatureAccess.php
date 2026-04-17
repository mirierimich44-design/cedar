<?php

namespace App\Http\Middleware;

use Closure;
use App\SaasSubscription;

class CheckFeatureAccess
{
    public function handle($request, Closure $next, string $featureKey)
    {
        $businessId = session('business.id');

        if (!$businessId) {
            return $next($request);
        }

        $subscription = SaasSubscription::with('features')
            ->where('business_id', $businessId)
            ->latest()
            ->first();

        // No subscription at all — allow access (legacy / pre-SaaS businesses)
        if (!$subscription) {
            return $next($request);
        }

        if (!$subscription->isAccessible()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Subscription expired.'], 403);
            }
            return redirect()->route('saas.portal')
                ->with('error', 'Your subscription has expired. Please renew to continue.');
        }

        if (!$subscription->hasFeature($featureKey)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Feature not included in your plan.'], 403);
            }
            return redirect()->back()
                ->with('error', 'This feature is not included in your current plan. <a href="' . route('saas.pricing') . '">Upgrade now</a>.');
        }

        return $next($request);
    }
}
