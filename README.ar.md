# حزمة تكامل Laravel Fastpay

توفر هذه الحزمة التكامل مع Fastpay ، مما يسمح لك ببدء المدفوعات والتحقق من صحة المدفوعات ومعالجة المبالغ المستردة والتحقق من صحة المبالغ المستردة
في تطبيق Laravel الخاص بك.

## تثبيت

يمكنك تثبيت الحزمة عبر الملحن:


```bash
composer require basit/laravel-fastpay-integration
```

انشر ملف التكوين باستخدام:

```bash
php artisan vendor:publish --tag="fastpay"
```

بعد تثبيت الحزمة ، تأكد من إضافة متغيرات البيئة التالية إلى ملف `.env` الخاص بك:

```env
FASTPAY_ENVIRONMENT=""
FASTPAY_STORE_ID=""
FASTPAY_STORE_PASSWORD=""
```

**ملاحظة:** تأكد من ضبط المتغير  "FASTPAY_ENVIRONMENT" في ملف ".env" إما على "staging" أو "production" بناءً على متطلباتك.

## الاستخدام

### 1. بدء الدفع

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

#### استجابة النجاح

```json
{
  "code": 200,
  "messages": ["Payment Initiation request processed successfully."],
  "data": {
    "redirect_uri": "https://staging-pgw.fast-pay.iq/pay?token=fc334490-348d-4040-87d9-dc33ae5xxxxx"
  }
}
```

## التحقق من صحة إشعار الدفع (عنوان URL الخاص بـ IPN)

يرسل Fastpay طلب POST إلى عنوان IPN الخاص بك مع معلومات الدفع قبل إعادة توجيه المستخدم إلى موقع الويب الخاص بك. هذا IPN
الإخطار ضروري للتحقق من صحة المعاملة ومنع المدفوعات الاحتيالية. يجب إعداد عنوان IPN الخاص بك للتعامل مع هذه الأمور
إخطارات الدفع وإجراء التحقق من الصحة بناءً على البيانات المستلمة.

عندما يرسل Fastpay إشعار IPN إلى عنوان IPN الخاص بك ، فإن طلب POST سيتضمن البيانات التالية:

- `gw_transaction_id`: المعرف الأبجدي الرقمي للمعاملة.
- `merchant_order_id`: المعرف الأبجدي الرقمي لطلبك (استبدل YOUR_ORDER_ID بهذه القيمة).
- `Received_amount`: قيمة عشرية تمثل مبلغ الدفعة المستلمة.
- `currency`: سلسلة تمثل العملة (على سبيل المثال ، دينار عراقي).
- `status`: سلسلة تمثل حالة الدفع (على سبيل المثال ، "تم بنجاح").
- `customer_name`: سلسلة تحتوي على اسم العميل.
- `customer_mobile_number`: سلسلة تحتوي على رقم هاتف العميل.
- `at`: تاريخ ووقت المعاملة بالصيغة" Y-m-d H: i: s "(على سبيل المثال ،" 2020-11-26 13:54:01 ").

يجب عليك استخدام هذه المعلومات للتحقق من صحة الدفع والتأكد من أن المعاملة حقيقية ودقيقة.

### 2. التحقق من صحة الدفع

```php
use Basit\FastpayPayment\Fastpay;

// Replace YOUR_ORDER_ID with the merchant_order_id received from the IPN callback
$merchantOrderId = "YOUR_ORDER_ID";

$response = Fastpay::validate($merchantOrderId);
```

#### استجابة النجاح

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

### 3. معالجة عملية استرداد الأموال

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

#### استجابة النجاح

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

### 4. التحقق من صحة رد الأموال

```php
use Basit\FastpayPayment\Fastpay;

// Replace YOUR_ORDER_ID with the merchant_order_id received from the IPN callback
$merchantOrderId = "YOUR_ORDER_ID";

$response = Fastpay::refundValidate($merchantOrderId);
```

#### استجابة النجاح

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

# التغييرات

يرجى مراجعة [CHANGELOG](CHANGELOG.md) للحصول على مزيد من المعلومات حول ما تم تغييره مؤخرًا.

## المساهمة

لا تتردد في المساهمة في هذا المشروع

## وجدت مشكلة أو خطأ؟

إذا واجهت أي مشاكل أو أخطاء أو كانت لديك أسئلة حول هذه الحزمة ، فنحن نشجعك على الإبلاغ عنها
 في صفحة [GitHub Issues](https://github.com/abdulbasit-dev/laravel-fastpay-integration/issues). يرجى التحقق مما إذا كان قد تم الإبلاغ عن المشكلة بالفعل قبل إنشاء مشكلة جديدة. عند الإبلاغ عن مشكلة ، قدم أكبر قدر ممكن من التفاصيل ، بما في ذلك خطوات إعادة إنتاجها ، والسلوك المتوقع ، والسلوك الفعلي.

نحن نقدر تعليقاتك ، وسنبذل قصارى جهدنا لمعالجة المشكلات وحلها في الوقت المناسب. مساهماتك ضرورية لتحسين هذه الحزمة للجميع.

## الاعتمادات

- [Abdulbasit Salah](https://github.com/abdulbasit-dev)

## رخصة
رخصة معهد ماساتشوستس للتكنولوجيا (MIT).
 الرجاء مراجعة [License File](LICENSE.md) للحصول على مزيد من المعلومات.
