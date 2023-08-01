<?php

namespace Basit\FastpayPayment\Interfaces;

interface FastpayInterfaces
{
    public static function initiate();

    public static function validate();

    public static function refund();

    public static function refundValid();
}
