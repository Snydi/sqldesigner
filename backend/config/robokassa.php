<?php

declare(strict_types=1);

return [
    'merchant_login' => env('ROBOKASSA_MERCHANT_LOGIN'),
    'password1' => env('ROBOKASSA_PASSWORD1'),
    'password2' => env('ROBOKASSA_PASSWORD2'),
    'hash_algorithm' => env('ROBOKASSA_HASH_ALGORITHM', 'md5'),
    'payment_url' => env('ROBOKASSA_PAYMENT_URL', 'https://auth.robokassa.ru/Merchant/Index.aspx'),
    'recurring_payment_url' => env('ROBOKASSA_RECURRING_PAYMENT_URL', 'https://auth.robokassa.ru/Merchant/Recurring'),
    'test_mode' => env('ROBOKASSA_TEST_MODE', true),
    'provider_amount' => env('ROBOKASSA_PROVIDER_AMOUNT'),
    'provider_currency' => 'RUB',
    'culture' => env('ROBOKASSA_CULTURE', 'en'),
    // Recurring charges require the initial payment to be made by bank card.
    'payment_method' => env('ROBOKASSA_PAYMENT_METHOD', 'BankCard'),
    'checkout_expires_minutes' => (int) env('ROBOKASSA_CHECKOUT_EXPIRES_MINUTES', 30),
    'receipt' => [
        'enabled' => env('ROBOKASSA_RECEIPT_ENABLED', true),
        'tax' => env('ROBOKASSA_RECEIPT_TAX', 'none'),
        'sno' => env('ROBOKASSA_RECEIPT_SNO'),
    ],
    'result_url' => env('ROBOKASSA_RESULT_URL', rtrim((string) env('APP_URL'), '/').'/api/webhooks/robokassa/result'),
    'success_url' => env('ROBOKASSA_SUCCESS_URL', rtrim((string) env('APP_URL'), '/').'/checkout/success'),
    'fail_url' => env('ROBOKASSA_FAIL_URL', rtrim((string) env('APP_URL'), '/').'/checkout/fail'),
];
