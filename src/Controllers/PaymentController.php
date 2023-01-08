<?php
namespace Src\Controllers;

use Src\Models\PaymentModel;
use Src\System\Encryption\Cryptor;
use Src\System\Response\Response;
use Src\System\Request\Request;
use Src\System\Validators\Validator;

class PaymentController {

    private $service = "payment";
    private $response;
    private $request;
    private $paymentModel;
    private $validator;
    private $cryptor;

    public $paymentTypeId = [
        'virtual_account' => 1,
        'credit_card' => 2
    ];

    public function __construct()
    {
        $this->response = new Response();
        $this->request = new Request();
        $this->paymentModel = new PaymentModel();
        $this->validator = new Validator();
        $this->cryptor = new Cryptor($_ENV['APP_KEY']);
    }

    public function create()
    {
        $input = $this->request->input();
        $errors = [];
        $invoice_id = $input['invoice_id'] ?? null;
        $item_name = $input['item_name'] ?? null;
        $amount = $input['amount'] ?? null;
        $payment_type = $input['payment_type'] ?? null;
        $customer_name = $input['customer_name'] ?? null;
        $merchant_id = $input['merchant_id'] ?? null;

        $exist_invoice = 0;
        $findInvoice = $this->paymentModel->findInvoiceId($invoice_id);
        if ($findInvoice['success']) {
            $exist_invoice = $findInvoice['data']['invoice_id'];
        }

        $this->validator->name('Invoice ID')
            ->value($invoice_id)
            ->required()
            ->unique([$exist_invoice])
            ->max(100);
        $this->validator->name('Item Name')
            ->value($item_name)
            ->required()
            ->max(100);
        $this->validator->name('Amount')
            ->value($amount)
            ->pattern('int')
            ->required();
        $this->validator->name('Payment Type')
            ->value($payment_type)
            ->paymentType()
            ->required();
        $this->validator->name('Customer Name')
            ->value($customer_name)
            ->required()
            ->max(100);
        $this->validator->name('Merchant ID')
            ->value($merchant_id)
            ->required()
            ->max(100);

        if (!$this->validator->validate()) {
            $errors = $this->validator->getErrors();
            return $this->response->badRequest($errors);
        }

        $input['payment_type'] = $this->paymentTypeId[$input['payment_type']];

        $makePayment = $this->paymentModel->makePayment($input);

        if ($makePayment['success']) {
            $payment_id = $makePayment['data']['payment_id'];
            $number_va = $makePayment['data']['payment_type'] == $this->paymentTypeId['virtual_account']
                ? rand(100000,999999)
                : null;
            $status = $makePayment['data']['payment_id'];
            return $this->response->success([
                'references_id' => $this->cryptor->encrypt($payment_id),
                'number_va' => $number_va,
                'status' => $this->paymentModel::statusName(0)
            ]);
        }

        return $this->response->serverError();

    }

    public function paymentInvoiceStatus()
    {
        $input = $this->request->input();

        $references_id = $input['references_id'] ?? null;
        $merchant_id = $input['merchant_id'] ?? null;

        $this->validator->name('References ID')
            ->value($references_id)
            ->required()
            ->max(100);

        if (!$this->validator->validate()) {
            $errors = $this->validator->getErrors();
            return $this->response->badRequest($errors);
        }

        $payment_id = $this->cryptor->decrypt($references_id);
        $findPaymentStatus = $this->paymentModel->findPaymentInvoiceStatus($payment_id);
        if ($findPaymentStatus['success']) {
            if ($findPaymentStatus['data']) {
                return $this->response->success([
                    'references_id' => $references_id,
                    'invoice_id' => $findPaymentStatus['data']['invoice_id'],
                    'status' => $this->paymentModel::statusName($findPaymentStatus['data']['transaction_status'])
                ]);
            }

            return $this->response->notFound('Invoice Not Found');
        }

        return $this->response->serverError();
        
    }
}