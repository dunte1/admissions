<?php

return [
    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
        'shortcode' => env('MPESA_SHORTCODE', ''),
        'passkey' => env('MPESA_PASSKEY', ''),
        'callback_url' => env('MPESA_CALLBACK_URL', ''),
        'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),
        'oauth_url' => env('MPESA_OAUTH_URL', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'),
        'stk_push_url' => env('MPESA_STK_PUSH_URL', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'),
        'stk_query_url' => env('MPESA_STK_QUERY_URL', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/query'),
        'timeout_url' => env('MPESA_TIMEOUT_URL', 'payment/timeout'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID', ''),
    ],

    'vonage' => [
        'sms_from' => env('VONAGE_SMS_FROM', 'KMTC'),
        'api_key' => env('VONAGE_API_KEY', ''),
        'api_secret' => env('VONAGE_API_SECRET', ''),
    ],

    'twilio' => [
        'sid' => env('TWILIO_ACCOUNT_SID', ''),
        'token' => env('TWILIO_AUTH_TOKEN', ''),
        'phone_number' => env('TWILIO_WHATSAPP_NUMBER', ''),
        'enabled' => env('TWILIO_WHATSAPP_ENABLED', false),
    ],

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY', ''),
        'api_url' => env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1'),
        'model' => env('OPENROUTER_MODEL', 'mistralai/mistral-7b-instruct'),
        'max_tokens' => env('OPENROUTER_MAX_TOKENS', 500),
        'temperature' => env('OPENROUTER_TEMPERATURE', 0.7),
        'store_conversations' => env('OPENROUTER_STORE_CONVERSATIONS', false),
    ],
];
