<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'docs*'], // acceptăm cereri către API și Docs

    'allowed_methods' => ['*'], // toate metodele HTTP: GET, POST, PUT etc

    'allowed_origins' => ['*'], // toate originile (localhost, 127.0.0.1 etc)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // toate headerele

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // dacă vrei să trimiți cookies, setezi true
];
