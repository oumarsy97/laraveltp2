<?php

return [
    'paths' => [
        'docs' => base_path('public/docs'), // Chemin où la documentation sera générée
        'annotations' => base_path('app/Http/Controllers'), // Chemin où les annotations sont situées
        'docs_json' => 'api-docs.json',
        'docs_yaml' => 'api-docs.yaml',
        'excludes' => [],
        'base' => env('APP_URL', 'http://localhost/api'),
    ],
    'host' => env('SWAGGER_HOST', 'localhost:3000'),
    'api_version' => env('SWAGGER_API_VERSION', '3.0.0'),
    'swagger_version' => env('SWAGGER_VERSION', '2.0'),
    'swagger' => env('SWAGGER', 'L5Swagger'),
    'title' => env('SWAGGER_TITLE', 'L5 Swagger'),
    'description' => env('SWAGGER_DESCRIPTION', 'L5 Swagger'),
    'contact_name' => env('SWAGGER_CONTACT_NAME', 'L5 Swagger'),
    'contact_email' => env('SWAGGER_CONTACT_EMAIL', 'L5 Swagger'),
    'contact_url' => env('SWAGGER_CONTACT_URL', 'L5 Swagger'),
];


