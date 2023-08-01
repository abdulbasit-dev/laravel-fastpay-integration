<?php

namespace Basit\FastpayPayment;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Basit\FastpayPayment\FastpayBaseClass;
use Illuminate\Support\Facades\Validator;

class Fastpay extends FastpayBaseClass
{
    public static function initiate($orderId, $cart) : array
    {
        try {
            // validate incoming parameters
            $validator = Validator::make(
                [
                    'order_id' => $orderId,
                    'cart' => $cart,
                ],
                [
                    'order_id' => 'required',
                    'cart' => ['required', 'array'],
                    'cart.*.name' => ['required', 'string'],
                    'cart.*.qty' => ['required', 'integer'],
                    'cart.*.unit_price' => ['required', 'integer'],
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    "code" => Response::HTTP_BAD_REQUEST,
                    'message' => 'Error',
                    'error' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            $billAmount = 0;

            // Iterate through the cart using references to modify each item
            foreach ($cart as &$item) {
                // Add the sub_total property to cart items
                $item['sub_total'] = $item['qty'] * $item['unit_price'];

                // Update the billAmount by adding the sub_total for each item
                $billAmount += $item['sub_total'];
            }

            // Encode the modified cart back to JSON
            $cart = json_encode($cart);

            $response = Http::post(self::baseUrl() . '/api/v1/public/pgw/payment/initiation', [
                "store_id" => config("fastpay.store_id"),
                "store_password" => config("fastpay.store_password"),
                "order_id" =>  $orderId,
                "bill_amount" => $billAmount,
                "currency" => "IQD",
                "cart" => $cart,
            ])->json();

            return $response;
        } catch (\Throwable $th) {

            return response()->json([
                "code" => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public static function validate($merchantOrderId) : array
    {
        try {
            $response = Http::post(self::baseUrl() . '/api/v1/public/pgw/payment/validate', [
                "store_id" => config("fastpay.store_id"),
                "store_password" => config("fastpay.store_password"),
                "order_id" => $merchantOrderId,
            ])->json();

            return $response;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function refund(string $merchantOrderId,string $msisdn, float $amount) : array
    {
        try {
            $response = Http::post(self::baseUrl() . '/api/v1/public/pgw/payment/refund', [
                "store_id"       => config("fastpay.store_id"),
                "store_password" => config("fastpay.store_password"),
                "order_id"       => $merchantOrderId,
                "msisdn"         => $msisdn,
                "amount"         => $amount,
            ])->json();

            return $response;
        } catch (\Throwable $th) {
            return response()->json([
                "code" => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public static function refundValidate(string $merchantOrderId) : array
    {
        try {
            $response = Http::post(self::baseUrl() . '/api/v1/public/pgw/payment/refund/validation', [
                "store_id"       => config("fastpay.store_id"),
                "store_password" => config("fastpay.store_password"),
                "order_id"       => $merchantOrderId,
            ])->json();

            return $response;
        } catch (\Throwable $th) {
            return response()->json([
                "code" => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
