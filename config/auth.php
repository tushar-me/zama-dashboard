<?php

return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'admin'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'admins'),
    ],
    'guards' => [
        'admin' => [ 
            'driver' => 'session',
            'provider' => 'admins',
        ],
        'vendor' => [
            'driver' => 'sanctum',
            'provider' =>'vendors',
        ],
    ],

    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\Admin::class),
        ],
        'vendros' => [
            'driver' => 'eloquent',
            'model' => env('VENDOR_AUTH_MODEL', App\Models\Vendor::class),
        ],
    ],
    'passwords' => [
        'admins' => [
            'provider' => 'admins',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 15,
        ],
        'vendors' => [
            'provider' => 'vendors',
            'table' => env('VENDOR_AUTH_PASSWORD_RESET_TOKEN_TABLE', 'vendor_password_resets'),
            'expire' => 60,
            'throttle' => 15,
        ],
    ],
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
