<?php

namespace Basit\FastpayPayment;

class FastpayBaseClass
{
    protected function baseUrl() :string
        {
            return config("fastpay.environment") == "production" ? "https://staging-apigw-merchant.fast-pay.iq" : "https://apigw-merchant.fast-pay.iq";
    }
}
