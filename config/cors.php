<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'upload', 'temp-images'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Thay bằng domain của bạn trong production
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,

];
