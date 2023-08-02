# Laravel Fastpay Integration Package

This package provides integration with Fastpay, allowing you to initiate payments, validate payments, process refunds, and validate refunds
in your Laravel application.

## Installation

You can install the package via composer:

```bash
composer require basit/laravel-fastpay-integration
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="fastpay"
```

After installing the package, make sure to add the following environment variables to your `.env` file:

```env
FASTPAY_ENVIRONMENT=""
FASTPAY_STORE_ID=""
FASTPAY_STORE_PASSWORD=""
```

**Note:** Ensure to set the `FASTPAY_ENVIRONMENT` variable in your `.env` file to either "staging" or "production" based on your requirements.

## Usage

### 1. Initiate Payment

```php
use Basit\FastpayPayment\Fastpay;

$cart = [
    [
        "name" => "Scarf",
        "qty" => 1,
        "unit_price" => 5000,
    ],
    [
        "name" => "T-Shirt",
        "qty" => 2,
        "unit_price" => 10000,
    ]
];

// Replace YOUR_ORDER_ID with the unique identifier for your customer's order
$orderId = "YOUR_ORDER_ID";

$response = Fastpay::initiate($orderId, $cart);
```

#### Success Response

```json
{
  "code": 200,
  "messages": ["Payment Initiation request processed successfully."],
  "data": {
    "redirect_uri": "https://staging-pgw.fast-pay.iq/pay?token=fc334490-348d-4040-87d9-dc33ae5xxxxx"
  }
}
```

## Payment Notification Validation (IPN URL)

Fastpay sends a POST request to your IPN URL with payment information before redirecting the user back to your website. This IPN
notification is essential for validating the transaction and preventing fraudulent payments. Your IPN URL should be set up to handle these
payment notifications and perform validation based on the received data.

When Fastpay sends an IPN notification to your IPN URL, the POST request will include the following data:

- `gw_transaction_id`: Alphanumeric identifier of the transaction.
- `merchant_order_id`: Alphanumeric identifier of your order (replace YOUR_ORDER_ID with this value).
- `received_amount`: Decimal value representing the received payment amount.
- `currency`: String representing the currency (e.g., IQD).
- `status`: String representing the status of the payment (e.g., "Success").
- `customer_name`: String containing the name of the customer.
- `customer_mobile_number`: String containing the customer's phone number.
- `at`: Date and time of the transaction in the format "Y-m-d H:i:s" (e.g., "2020-11-26 13:54:01").

You should use this information to validate the payment and ensure the transaction is genuine and accurate.

### 2. Validate Payment

```php
use Basit\FastpayPayment\Fastpay;

// Replace YOUR_ORDER_ID with the merchant_order_id received from the IPN callback
$merchantOrderId = "YOUR_ORDER_ID";

$response = Fastpay::validate($merchantOrderId);
```

#### Success Response

```json
{
  "code": 200,
  "messages": [],
  "data": {
    "gw_transaction_id": "CUL1NUB713",
    "merchant_order_id": "LAREVEORD1005",
    "received_amount": "5000.00",
    "currency": "IQD",
    "customer_name": "John Doe",
    "customer_mobile_number": "+964xxxxxxxxxx",
    "at": "2023-06-14 18:06:30",
    "transaction_id": "AXGOSG5527",
    "order_id": "516867551564444475",
    "customer_account_no": "+964xxxxxxxxxx",
    "status": "Success",
    "received_at": "2023-06-14 18:06:30"
  }
}
```

### 3. Process Refund

```php
use Basit\FastpayPayment\Fastpay;

// Replace YOUR_ORDER_ID with the merchant_order_id for the order to be refunded
$merchantOrderId = "YOUR_ORDER_ID";

// Replace CUSTOMER_PHONE_NUMBER with the customer's phone number
$msisdn = "CUSTOMER_PHONE_NUMBER";

// Replace AMOUNT_TO_REFUND with the amount to be refunded (float)
$amount = AMOUNT_TO_REFUND;

$response = Fastpay::refund($merchantOrderId, $msisdn, $amount);
```

#### Success Response

```json
{
  "code": 200,
  "messages": [],
  "data": {
    "summary": {
      "recipient": {
        "name": "John Doe",
        "mobile_number": "+9640101010101",
        "avatar": "https://revamp.fast-pay.iq/image/revamp.jpg",
      }
      "refund_invoice_id": "AUJHMA1634"
    }
  }
}
```

### 4. Validate Refund

```php
use Basit\FastpayPayment\Fastpay;

// Replace YOUR_ORDER_ID with the merchant_order_id received from the IPN callback
$merchantOrderId = "YOUR_ORDER_ID";

$response = Fastpay::refundValidate($merchantOrderId);
```

#### Success Response

```json
{
  "code": 200,
  "messages": [],
  "data": {
    "order_id": "LAREVEORD5006",
    "refund_status": true,
    "status_checked_at": "2021-03-01 00:00:05"
  }
}
```

## Contributing

Feel free to contribute to this project

## Found an Issue or Bug?

If you encounter any issues, bugs, or have questions about this package, we encourage you to report them on our [GitHub Issues](https://github.com/abdulbasit-dev/laravel-fastpay-integration/issues) page. Please check if the issue has already been reported before creating a new one. When reporting an issue, provide as much detail as possible, including the steps to reproduce it, the expected behavior, and the actual behavior.

We value your feedback, and we'll do our best to address and resolve the issues in a timely manner. Your contributions are essential to improving this package for everyone.


## Credits

- [Abdulbasit Salah](https://github.com/abdulbasit-dev)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
