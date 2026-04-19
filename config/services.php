<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'africastalking' => [
        'username' => env('AFRICASTALKING_USERNAME'),
        'api_key' => env('AFRICASTALKING_API_KEY'),
        'whatsapp_from' => env('AFRICASTALKING_WHATSAPP_FROM'),
    ],

    // Platform-level Daraja credentials used by SaaS billing (ApexPOS owner paybill)
    'saas_daraja' => [
        'env'              => env('SAAS_DARAJA_ENV', 'sandbox'), // sandbox|production
        'consumer_key'     => env('SAAS_DARAJA_CONSUMER_KEY'),
        'consumer_secret'  => env('SAAS_DARAJA_CONSUMER_SECRET'),
        'shortcode'        => env('SAAS_DARAJA_SHORTCODE'),       // paybill/till
        'passkey'          => env('SAAS_DARAJA_PASSKEY'),
        'callback_url'     => env('SAAS_DARAJA_CALLBACK_URL'),    // must be publicly reachable HTTPS
        'transaction_type' => env('SAAS_DARAJA_TX_TYPE', 'CustomerPayBillOnline'),
    ],

];
