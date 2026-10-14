<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Keys for Web Push Notifications
    |--------------------------------------------------------------------------
    |
    | VAPID (Voluntary Application Server Identification) keys are used
    | to identify your application server to the browser push services.
    |
    */
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@lifeplanner.local'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guzzle / Client Options for WebPush
    |--------------------------------------------------------------------------
    */
    'client_options' => [
        'timeout' => 15,
        'verify' => env('WEBPUSH_SSL_VERIFY', false),
    ],
];
