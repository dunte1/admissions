<?php

return [
    'defaults' => [
        'domain' => env('TENANT_DEFAULT_DOMAIN', 'admission-portal.com'),
        'subdomain_suffix' => env('TENANT_SUBDOMAIN_SUFFIX', '.admission-portal.com'),
    ],

    'main_platform' => [
        'domain' => env('PLATFORM_DOMAIN', 'admission-portal.com'),
        'url' => env('PLATFORM_URL', 'https://admission-portal.com'),
        'routes_prefix' => 'super-admin',
    ],

    'tenant' => [
        'auth_guard' => 'web',
        'session_prefix' => 'tenant_',
        'cache_prefix' => 'tenant:',
    ],

    'features' => [
        'default_enabled' => [
            'admissions',
            'messaging',
            'notifications',
        ],
        'available' => [
            'admissions' => 'Admissions Portal',
            'online_exams' => 'Online Exams',
            'finance' => 'Finance Module',
            'library' => 'Library Module',
            'notifications' => 'Notifications',
            'ai_chatbot' => 'AI Chatbot',
            'messaging' => 'Messaging',
            'document_verification' => 'Document Verification',
            'admission_letters' => 'Admission Letters',
            'analytics' => 'Analytics',
        ],
    ],

    'domains' => [
        'enable_custom_domains' => true,
        'enable_subdomains' => true,
        'require_ssl' => false,
    ],

    'branding' => [
        'cache_ttl' => 3600,
        'default_logo' => '/images/logo.png',
        'default_favicon' => '/favicon.ico',
    ],
];