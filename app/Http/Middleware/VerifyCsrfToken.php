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
        // React mobile POS app — uses session cookie auth, no CSRF token needed
        'login',
        'logout',
        'mobile-pos-details',
        'products/list',
        'pos',
        'pos/*',
        'contacts/customers',
    ];
}
