<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/install/details',
        '/install/post-details',
        '/install/install-alternate',
        '/api/ecom/customers',
        '/api/ecom/orders',
        '/webhook/*',
        'mobile-money/webhook/*',
        'saas/mpesa/callback/*',
        'pesapal/ipn',
        'pesapal/callback',
        'sync/pull',     // device-token authenticated, no session
        'sync/push',     // device-token authenticated, no session
        'sync/status',   // device-token authenticated, no session
        'sync/settings', // settings saved via AJAX (auth-gated separately)
        'sync/*',        // OPTIONS pre-flight for cross-domain (Laragon)
    ];
}
