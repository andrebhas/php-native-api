<?php

require_once __DIR__ . "/../bootstrap.php";

use Src\Models\PaymentModel;

$input['invoice_id'] = date('Ymd') . rand(1,1000);
$tiket = array("tiket a", "tiket b");
$input['item_name'] = $tiket[array_rand($tiket)];
$input['amount'] = rand(100000,1000000);
$input['payment_type'] = rand(1,2);
$customers = array("customer a", "customer b");
$input['customer_name'] = $customers[array_rand($customers)];
$merchants = array("merchant a", "merchant b");
$input['merchant_id'] = $merchants[array_rand($merchants)];

$paymentModel = new PaymentModel;
$createPayment = $paymentModel->makePayment($input);
if ($createPayment['success']) {
    echo "Payment Seeder Success";
} else {
    echo "Payment Seeder Fail";
}

exit();
