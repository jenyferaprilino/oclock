<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'signature_key' => env('MIDTRANS_SIGNATURE_KEY', env('MIDTRANS_SIGNATURE_SECRET_KEY')),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'checkout_enabled' => env('MIDTRANS_CHECKOUT_ENABLED', false),
    'expiry_minutes' => 30,
];
