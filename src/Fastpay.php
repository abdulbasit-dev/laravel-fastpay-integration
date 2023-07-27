<?php

namespace Basit\FastpayIntegration;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Basit\FastpayIntegration\FastpayBaseClass;
use Basit\FastpayIntegration\Interfaces\FastpayInterfaces;

// class Fastpay extends FastpayBaseClass implements FastpayInterfaces
class Fastpay extends FastpayBaseClass
{
    public function initiate($orderId, $qty, $unitPrice, $totalPrice)
    {
        // validate incoming parameters

        try {
            $response = Http::post(self::baseUrl() . '/api/v1/public/pgw/payment/initiation', [
                "store_id" => config("fastpay.store_id"),
                "store_password" => config("fastpay.store_password"),
                "order_id" =>  $orderId,
                "bill_amount" => $totalPrice,
                "currency" => "IQD",
                "cart" => '[{"name":"Ticket","qty":' . $qty . ',"unit_price":' . $unitPrice . ',"sub_total":' . $totalPrice . '}]'
            ])->json();

            if ($response['code'] == 200) {
                return ($response['data']['redirect_uri']);
            } else {
                Log::error($response);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function validate($merchantOrderId)
    {
        try {
            // call fastpay validation api
            $response = Http::post(self::baseUrl() . '/api/v1/public/pgw/payment/validate', [
                "store_id" => config("fastpay.store_id"),
                "store_password" => config("fastpay.store_password"),
                "order_id" => $merchantOrderId,
            ])->json();

            // if payment is success, update order status to paid
            if ($response['code'] == 200) {
                return  true;
            } else {
                Log::error($response);
            }
            return 1;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
