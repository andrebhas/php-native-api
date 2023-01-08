<?php
use Src\System\Routes\Router;
use Src\System\Response\Response;
use Src\Controllers\PaymentController;

$paymentController = new PaymentController;

Router::get('/', function () {
    (new Response())->success("PHP Native API");
});

Router::post('/payment', function () use ($paymentController){
    $paymentController->create();
});

Router::get('/payment/invoice-status', function () use ($paymentController){
    $paymentController->paymentInvoiceStatus();
});

Router::run();