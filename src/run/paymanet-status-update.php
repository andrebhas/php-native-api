<?php

require_once __DIR__ . "/../bootstrap.php";

use Src\Models\PaymentModel;
use Src\System\Encryption\Cryptor;

$filters = array_fill(0, 2, null);
for($i = 1; $i < $argc; $i++) {
    $filters[$i - 1] = $argv[$i];
}
$references_id = $filters[0];
$status = $filters[1];

$acceptedStatus = ['pending', 'paid', 'failed'];

if (!in_array(strtolower($status), $acceptedStatus)) {
    echo "Failed! The accepted status parameters should be " . implode(', ', $acceptedStatus);
    exit;
}

$paymentModel = new PaymentModel();
$transaction_status = $paymentModel->paymentStatusId[$status];

$cryptor = new Cryptor($_ENV['APP_KEY']);
$payment_id = $cryptor->decrypt($references_id);

$update = $paymentModel->updateStatusPayment($payment_id, $transaction_status);

echo "Update Payment Success! references_id: {$references_id} status: {$status}";
