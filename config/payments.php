<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for various payment gateways available in Africa
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'stripe'),

    'gateways' => [
        
        // Stripe (International + Afrique)
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', true),
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'countries' => ['*'], // Disponible partout
        ],

        // Paystack (Nigeria, Ghana, South Africa, Kenya)
        'paystack' => [
            'enabled' => env('PAYSTACK_ENABLED', false),
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
            'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
            'countries' => ['NG', 'GH', 'ZA', 'KE'],
        ],

        // Flutterwave (Pan-African)
        'flutterwave' => [
            'enabled' => env('FLUTTERWAVE_ENABLED', false),
            'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
            'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY'),
            'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_SECRET'),
            'countries' => ['*'], // Pan-African
        ],

        // Wave (Sénégal, Côte d'Ivoire, Mali, Burkina Faso, Bénin, Togo)
        'wave' => [
            'enabled' => env('WAVE_ENABLED', false),
            'api_key' => env('WAVE_API_KEY'),
            'secret_key' => env('WAVE_SECRET_KEY'),
            'webhook_secret' => env('WAVE_WEBHOOK_SECRET'),
            'countries' => ['SN', 'CI', 'ML', 'BF', 'BJ', 'TG'],
        ],

        // Orange Money (Afrique Francophone)
        'orange_money' => [
            'enabled' => env('ORANGE_MONEY_ENABLED', false),
            'merchant_key' => env('ORANGE_MONEY_MERCHANT_KEY'),
            'api_user' => env('ORANGE_MONEY_API_USER'),
            'api_password' => env('ORANGE_MONEY_API_PASSWORD'),
            'countries' => ['SN', 'CI', 'ML', 'BF', 'NE', 'GN', 'CM', 'CD', 'MG', 'CF'],
        ],

        // MTN Mobile Money (Afrique de l'Ouest et Centrale)
        'mtn_momo' => [
            'enabled' => env('MTN_MOMO_ENABLED', false),
            'api_key' => env('MTN_MOMO_API_KEY'),
            'user_id' => env('MTN_MOMO_USER_ID'),
            'subscription_key' => env('MTN_MOMO_SUBSCRIPTION_KEY'),
            'countries' => ['GH', 'UG', 'RW', 'ZM', 'CI', 'CM', 'BJ', 'CD', 'CG', 'SZ'],
        ],

        // Airtel Money (Pan-African)
        'airtel_money' => [
            'enabled' => env('AIRTEL_MONEY_ENABLED', false),
            'client_id' => env('AIRTEL_MONEY_CLIENT_ID'),
            'client_secret' => env('AIRTEL_MONEY_CLIENT_SECRET'),
            'countries' => ['KE', 'TZ', 'UG', 'RW', 'ZM', 'MW', 'NG', 'CD', 'GA', 'NE', 'TD', 'MG'],
        ],

        // Moov Money (Afrique de l'Ouest)
        'moov_money' => [
            'enabled' => env('MOOV_MONEY_ENABLED', false),
            'api_key' => env('MOOV_MONEY_API_KEY'),
            'secret_key' => env('MOOV_MONEY_SECRET_KEY'),
            'countries' => ['BJ', 'BF', 'CI', 'TG', 'NE'],
        ],

        // M-Pesa (Kenya, Tanzania, Mozambique, Lesotho)
        'mpesa' => [
            'enabled' => env('MPESA_ENABLED', false),
            'consumer_key' => env('MPESA_CONSUMER_KEY'),
            'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
            'shortcode' => env('MPESA_SHORTCODE'),
            'passkey' => env('MPESA_PASSKEY'),
            'countries' => ['KE', 'TZ', 'MZ', 'LS'],
        ],

        // Chipper Cash (Pan-African)
        'chipper' => [
            'enabled' => env('CHIPPER_ENABLED', false),
            'api_key' => env('CHIPPER_API_KEY'),
            'secret_key' => env('CHIPPER_SECRET_KEY'),
            'countries' => ['NG', 'GH', 'KE', 'UG', 'TZ', 'RW', 'ZA'],
        ],

        // DPO PayGate (Afrique du Sud + Pan-African)
        'dpo' => [
            'enabled' => env('DPO_ENABLED', false),
            'company_token' => env('DPO_COMPANY_TOKEN'),
            'service_type' => env('DPO_SERVICE_TYPE'),
            'countries' => ['ZA', 'KE', 'UG', 'TZ', 'BW', 'ZM', 'ZW', 'MU'],
        ],

        // Fedapay (Bénin, Togo, Côte d'Ivoire, Sénégal)
        'fedapay' => [
            'enabled' => env('FEDAPAY_ENABLED', false),
            'public_key' => env('FEDAPAY_PUBLIC_KEY'),
            'secret_key' => env('FEDAPAY_SECRET_KEY'),
            'webhook_secret' => env('FEDAPAY_WEBHOOK_SECRET'),
            'countries' => ['BJ', 'TG', 'CI', 'SN'],
        ],

        // Kkiapay (Bénin, Togo, Côte d'Ivoire, Sénégal, Burkina Faso)
        'kkiapay' => [
            'enabled' => env('KKIAPAY_ENABLED', false),
            'public_key' => env('KKIAPAY_PUBLIC_KEY'),
            'private_key' => env('KKIAPAY_PRIVATE_KEY'),
            'secret' => env('KKIAPAY_SECRET'),
            'countries' => ['BJ', 'TG', 'CI', 'SN', 'BF'],
        ],

        // CinetPay (Côte d'Ivoire + Afrique de l'Ouest)
        'cinetpay' => [
            'enabled' => env('CINETPAY_ENABLED', false),
            'api_key' => env('CINETPAY_API_KEY'),
            'site_id' => env('CINETPAY_SITE_ID'),
            'countries' => ['CI', 'SN', 'BJ', 'BF', 'TG', 'ML', 'NE', 'GN', 'CM'],
        ],

        // PayDunya (Afrique de l'Ouest)
        'paydunya' => [
            'enabled' => env('PAYDUNYA_ENABLED', false),
            'master_key' => env('PAYDUNYA_MASTER_KEY'),
            'public_key' => env('PAYDUNYA_PUBLIC_KEY'),
            'private_key' => env('PAYDUNYA_PRIVATE_KEY'),
            'token' => env('PAYDUNYA_TOKEN'),
            'countries' => ['SN', 'CI', 'BJ', 'TG', 'GH', 'BF'],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile Money Providers by Country
    |--------------------------------------------------------------------------
    */

    'mobile_money' => [
        'SN' => ['Orange Money', 'Wave', 'Free Money'],
        'CI' => ['Orange Money', 'MTN Mobile Money', 'Moov Money', 'Wave'],
        'BJ' => ['MTN Mobile Money', 'Moov Money'],
        'BF' => ['Orange Money', 'Moov Money'],
        'TG' => ['Togocel TMoney', 'Moov Money'],
        'ML' => ['Orange Money', 'Mobicash'],
        'NG' => ['MTN Mobile Money', 'Airtel Money', 'Glo Mobile Money'],
        'GH' => ['MTN Mobile Money', 'Vodafone Cash', 'AirtelTigo Money'],
        'KE' => ['M-Pesa', 'Airtel Money', 'Equitel'],
        'TZ' => ['M-Pesa', 'Airtel Money', 'Tigo Pesa', 'Halo Pesa'],
        'UG' => ['MTN Mobile Money', 'Airtel Money'],
        'RW' => ['MTN Mobile Money', 'Airtel Money'],
        'ZA' => ['MTN Mobile Money', 'Vodacom M-Pesa'],
        'CD' => ['Orange Money', 'Airtel Money', 'M-Pesa', 'Afrimoney'],
        'CM' => ['Orange Money', 'MTN Mobile Money'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies by Country
    |--------------------------------------------------------------------------
    */

    'currencies' => [
        'XOF' => ['SN', 'CI', 'BJ', 'BF', 'TG', 'ML', 'NE'], // Franc CFA (Afrique de l'Ouest)
        'XAF' => ['CM', 'GA', 'CG', 'TD', 'CF', 'GQ'], // Franc CFA (Afrique Centrale)
        'NGN' => ['NG'], // Naira nigérian
        'GHS' => ['GH'], // Cedi ghanéen
        'KES' => ['KE'], // Shilling kenyan
        'TZS' => ['TZ'], // Shilling tanzanien
        'UGX' => ['UG'], // Shilling ougandais
        'RWF' => ['RW'], // Franc rwandais
        'ZAR' => ['ZA'], // Rand sud-africain
        'MAD' => ['MA'], // Dirham marocain
        'TND' => ['TN'], // Dinar tunisien
        'EGP' => ['EG'], // Livre égyptienne
        'ETB' => ['ET'], // Birr éthiopien
        'ZMW' => ['ZM'], // Kwacha zambien
        'MGA' => ['MG'], // Ariary malgache
        'EUR' => ['*'], // Euro (accepté partout)
        'USD' => ['*'], // Dollar US (accepté partout)
    ],

];
