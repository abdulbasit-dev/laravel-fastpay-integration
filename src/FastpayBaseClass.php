<?php

namespace Basit\FastpayPayment;

class FastpayBaseClass
{
    protected static function baseUrl() :string
        {
            return config("fastpay.environment") == "production" ? "https://apigw-merchant.fast-pay.iq": "https://staging-apigw-merchant.fast-pay.iq";
    }
}
