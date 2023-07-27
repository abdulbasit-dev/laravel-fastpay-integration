<?php

namespace Basit\FastpayPayment\Interfaces;

interface FastpayInterfaces
{
    public function initiate();

    public function validate();

    public function refund();

    public function refundValid();
}
