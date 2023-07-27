<?php

namespace Basit\FastpayPayment\Facades;

use Illuminate\Support\Facades\Facade;


class Fastpay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fastpay';
        return Basit\FastpayIntegration\Fastpay::class;
    }
}
