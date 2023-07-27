<?php

return [
    /*
   |--------------------------------------------------------------------------
   | Fastpay Environment
   |--------------------------------------------------------------------------
   |
   | This value is the environment that you want to choose for Fastpay integration to your application.
   | values are (staging, production)
   |
   */

    'environment' => env('FASTPAY_ENVIRONMENT', 'stage'),

    /*
   |--------------------------------------------------------------------------
   | Credentials
   |--------------------------------------------------------------------------
   |
   | The account credentials you use to authenticate the request determines whether the request is live mode or test mode
   | Fastpay credentials are required to perform a transaction.
   */

    "store_id" => env('FASTPAY_STORE_ID'),
    "store_password" => env('FASTPAY_STORE_PASSWORD'),
];
