# پاکێجی یەکخستنی فاستپەی لاراڤێل

ئەم پاکێجە یەکگرتن لەگەڵ فاستپەی دابین دەکات، ڕێگەت پێدەدات دەست بە پارەدان بکەیت، پارەدانەکان پشتڕاست بکەیتەوە، پرۆسێسکردنی گەڕاندنەوەی پارە و پشتڕاستکردنەوەی گەڕاندنەوە
لە ئەپڵیکەیشنی لاراڤێلەکەتدا.

## دامەزراندن

دەتوانیت لە ڕێگەی composerەوە پاکێجەکە دابمەزرێنیت:

```bash
composer require basit/laravel-fastpay-integration
```

فایلە ڕێکخستنەکە بەم شێوەیە بڵاوبکەرەوە:

```bash
php artisan vendor:publish --tag="fastpay"
```

دوای دامەزراندنی پاکێجەکە، دڵنیابە کە ئەم گۆڕاوە ژینگەییانەی خوارەوە زیاد دەکەیت بۆ پەڕگەی `.env` ـەکەت:

```env
FASTPAY_ENVIRONMENT=""
FASTPAY_STORE_ID=""
FASTPAY_STORE_PASSWORD=""
```

**تێبینی:** دڵنیابە لە ڕێکخستنی گۆڕاوەی `FASTPAY_ENVIRONMENT` لە پەڕگەی `.env` ـەکەتدا بۆ "staging" یان "production" بە پشتبەستن بە پێداویستییەکانتان.

## بەکارهێنان

### 1. دەستپێکردنی پارەدان

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

#### وەڵامدانەوەی دروست

```json
{
  "code": 200,
  "messages": ["Payment Initiation request processed successfully."],
  "data": {
    "redirect_uri": "https://staging-pgw.fast-pay.iq/pay?token=fc334490-348d-4040-87d9-dc33ae5xxxxx"
  }
}
```

## چەسپاندنی ئاگادارکردنەوەی پارەدان (IPN URL)

فاستپەی داواکاری POST دەنێرێت بۆ URL IPN ی تۆ لەگەڵ زانیاری پارەدان پێش ئەوەی بەکارهێنەرەکە ئاڕاستە بکاتەوە بۆ ماڵپەڕەکەت.
ئەم ئاگادارکردنەوە IPN زۆر گرنگە بۆ چەسپاندنی مامەڵەکە و ڕێگریکردن لە پارەدانی ساختە.
پێویستە URLی IPN ی تۆ ڕێکبخرێت بۆ مامەڵەکردن لەگەڵ ئەم ئاگادارکردنەوە پارەدانانە و ئەنجامدانی چەسپاندن لەسەر بنەمای داتا وەرگیراوەکان.

کاتێک Fastpay ئاگادارکردنەوەیەکی IPN دەنێرێت بۆ URL IPN ی تۆ، داواکاری POST ئەم زانیاریانەی خوارەوە لەخۆدەگرێت:

- `gw_transaction_id`: ناسێنەری ئەلفوبێی ژمارەیی مامەڵەکە.
- `merchant_order_id`: ناسێنەری ئەلفوبێی ژمارەیی داواکارییەکەت (YOUR_ORDER_ID بەم بەهایە بگۆڕە).
- `received_amount`: بەهای دەهەمی کە نوێنەرایەتی بڕی پارەدانی وەرگیراو دەکات.
- `currency`: ڕستەیەک کە نوێنەرایەتی دراوەکە دەکات (بۆ نموونە، IQD).
- `status`: ڕیزێک کە نوێنەرایەتی دۆخی پارەکە دەکات (بۆ نموونە، "سەرکەوتن").
- `customer_name`: ڕستەیەک کە ناوی کڕیارەکەی تێدایە.
- `customer_mobile_number`: ڕیزێک کە ژمارەی تەلەفۆنی کڕیارەکەی تێدایە.
- `at`: بەروار و کاتی مامەڵەکە بە شێوەی "Y-m-d H:i:s" (بۆ نموونە، "2020-11-26 13:54:01").

پێویستە ئەم زانیاریانە بەکاربهێنیت بۆ چەسپاندنی پارەکە و دڵنیابوون لەوەی مامەڵەکە ڕاستەقینە و وردە.

### 2. پشتڕاستکردنەوەی پارەدان

```php
use Basit\FastpayPayment\Fastpay;

// Replace YOUR_ORDER_ID with the merchant_order_id received from the IPN callback
$merchantOrderId = "YOUR_ORDER_ID";

$response = Fastpay::validate($merchantOrderId);
```

#### وەڵامدانەوەی دروست

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

### 3. پرۆسەی گەڕاندنەوەی پارە

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

#### وەڵامدانەوەی دروست

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

### 4. پشتڕاستکردنەوەی گەڕانەوەی پارە

```php
use Basit\FastpayPayment\Fastpay;

// Replace YOUR_ORDER_ID with the merchant_order_id received from the IPN callback
$merchantOrderId = "YOUR_ORDER_ID";

$response = Fastpay::refundValidate($merchantOrderId);
```

#### وەڵامدانەوەی دروست

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

# گۆڕانکارییەکان

تکایە سەیری [CHANGELOG](CHANGELOG.md) بکە بۆ زانیاری زیاتر لەسەر ئەوەی کە لەم دواییانەدا چی گۆڕاوە.

## بەشداریکردن

ئازادانە بەشداری لەم پڕۆژەیەدا بکەن

## کێشەیەک یان هەڵەیەکت دۆزیەوە؟

ئەگەر تووشی هەر کێشەیەک بوویت، هەڵەیەک، یان پرسیارت هەیە سەبارەت بەم پاکێجە، ئێمە هانتان دەدەین کە لە لاپەڕەی [GitHub Issues](https://github.com/abdulbasit-dev/laravel-fastpay-integration/issues) ڕاپۆرتی بکەن. تکایە پێش دروستکردنی کێشەیەکی نوێ بزانە کە ئایا کێشەکە پێشتر ڕاپۆرت کراوە یان نا. لە کاتی ڕاپۆرتکردنی کێشەیەکدا، تا دەتوانیت وردەکارییەکان بخەرە ڕوو، لەوانە هەنگاوەکانی دووبارە بەرهەمهێنانەوەی، ڕەفتاری چاوەڕوانکراو و ڕەفتاری ڕاستەقینە.

ئێمە بەهای فیدباکەکانتان دەزانین، و هەموو هەوڵێک دەدەین بۆ چارەسەرکردن و چارەسەرکردنی کێشەکان لە کاتی خۆیدا. بەشدارییەکانتان زۆر گرنگن بۆ باشترکردنی ئەم پاکێجە بۆ هەمووان.

## کریدتەکان

- [Abdulbasit Salah](https://github.com/abdulbasit-dev)

## مۆڵەت

مۆڵەتی MIT (MIT). تکایە بۆ زانیاری زیاتر سەیری [پەڕگەی مۆڵەت](LICENSE.md) بکە.
