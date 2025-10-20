
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Pastikan environment dan key sudah benar.
    | Key bisa tanpa prefix SB- (Midtrans versi baru).
    |
    */

    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'G445836031'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-...'),
    'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-...'),

    // Set false untuk sandbox, true untuk production
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Optional settings
    'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/v1/transactions'),
];
