<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],

    // On autorise plusieurs origines : le frontend local (développement)
    // ET le frontend déployé sur Vercel (production). array_filter retire
    // les valeurs vides si FRONTEND_URL_PROD n'est pas encore défini.
    'allowed_origins' => array_filter([
        env('FRONTEND_URL', 'http://localhost:3000'),
        env('FRONTEND_URL_PROD'),
    ]),

    'allowed_origins_patterns' => [
        // Autorise TOUTES les URL Vercel de ce projet (production + chaque
        // déploiement de prévisualisation, qui ont chacun une URL unique
        // du type projet-stage-front-XXXXX-ameyikpos-projects.vercel.app).
        '#^https://projet-stage-front-.*\.vercel\.app$#',
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];