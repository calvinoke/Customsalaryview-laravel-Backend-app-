<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
          |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register','reset-password'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:3000','https://ec2-16-170-163-154.eu-north-1.compute.amazonaws.com','http://localhost:8000'],


    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
//return [

  //  'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Only allow your frontend domain to make requests
  //  'allowed_origins' => ['https://your-frontend.com'],

   // 'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    //'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization', 'Accept', 'Origin'],

    // Expose any headers you want your frontend to access (e.g. pagination info)
    //'exposed_headers' => [],

    //'max_age' => 3600,  // Cache preflight response for 1 hour

    // Allow credentials (cookies, auth headers) to be sent
    //'supports_credentials' => true,

//];
