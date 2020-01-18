<?php
return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=storeconsent',
        // 'dsn' => 'sqlite:/' . __DIR__ . '/../consents.sqlite',
        'user' => 'root',
        'password' => 'root',
    ],
    'base_path' => '',
    'consent_categories' =>[
        'essential',
        'statistics',
        'marketing',
        'external_media',
    ],
];