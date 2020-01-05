<?php
return [
    'db_dsn' => 'sqlite:/' . __DIR__ . '/../consents.sqlite',
    'base_path' => '',
    'consent_categories' =>[
        'essential',
        'statistics',
        'marketing',
        'external_media',
    ],
];