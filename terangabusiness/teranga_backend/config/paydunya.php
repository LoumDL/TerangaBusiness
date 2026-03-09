<?php

return [
    'sandbox'      => env('PAYDUNYA_SANDBOX', true),
    'master_key'   => env('PAYDUNYA_MASTER_KEY', ''),
    'private_key'  => env('PAYDUNYA_PRIVATE_KEY', ''),
    'token'        => env('PAYDUNYA_TOKEN', ''),
    'public_key'   => env('PAYDUNYA_PUBLIC_KEY', ''),
    'ipn_secret'   => env('PAYDUNYA_IPN_SECRET', ''),
    'callback_url' => env('PAYDUNYA_CALLBACK_URL', env('APP_URL') . '/api/v1/paiements/webhook'),
    'return_url'   => env('PAYDUNYA_RETURN_URL', env('FRONTEND_URL', 'http://localhost:3000') . '/paiement/succes'),
    'cancel_url'   => env('PAYDUNYA_CANCEL_URL', env('FRONTEND_URL', 'http://localhost:3000') . '/paiement/annule'),
];
