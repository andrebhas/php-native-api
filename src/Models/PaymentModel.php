<?php

namespace Src\Models;

use Src\System\Database\DatabaseConnector;
use Src\System\Logger\Log;

class PaymentModel
{
    private $service = "payment";
    private $db = null;
    private $paymentTable = "payments";
    private $paymentStatusTable = "payment_status";
    private $log;

    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_FAILED = 2;

    public $paymentStatusId = [
        'pending' => self::STATUS_PENDING,
        'paid' => self::STATUS_PAID,
        'failed' => self::STATUS_FAILED
    ];

    public function __construct()
    {
        $this->db = (new DatabaseConnector)->getConnection();
        $this->log = (new Log($this->service))->run();
    }

    public function insertPayment(array $input)
    {
        try {
            $sql = "INSERT INTO {$this->paymentTable}
                (invoice_id, item_name, amount, payment_type, customer_name, merchant_id)
                VALUES
                (:invoice_id, :item_name, :amount, :payment_type, :customer_name, :merchant_id)";

            $statement = $this->db->prepare($sql);

            $statement->bindParam(':invoice_id', $input['invoice_id']);
            $statement->bindParam(':item_name', $input['item_name']);
            $statement->bindParam(':amount', $input['amount']);
            $statement->bindParam(':payment_type', $input['payment_type']);
            $statement->bindParam(':customer_name', $input['customer_name']);
            $statement->bindParam(':merchant_id', $input['merchant_id']);

            $statement->execute();

            return [
                'success' => true,
                'data' => [
                    'last_id' => $this->db->lastInsertId()
                ]
            ];
        } catch (\PDOException $e) {
            $this->log->error($e->getMessage());
            return [
                'success' => false
            ];
        }
    }

    public function insertStatus($payment_id, $status)
    {
        try {
            $sql = "INSERT INTO {$this->paymentStatusTable}
                (payment_id, status)
                VALUES
                (:payment_id, :status)";

            $statement = $this->db->prepare($sql);

            $statement->bindParam(':payment_id', $payment_id);
            $statement->bindParam(':status', $status);

            $statement->execute();

            return [
                'success' => true,
                'data' => [
                    'last_id' => $this->db->lastInsertId()
                ]
            ];
        } catch (\PDOException $e) {
            $this->log->error($e->getMessage());
            return [
                'success' => false
            ];
        }
    }

    public function makePayment(array $input)
    {
        try {
            $this->db->beginTransaction();

            $insertPayment = $this->insertPayment($input);
            if ($insertPayment['success']) {
                $input['payment_id'] = $insertPayment['data']['last_id'] ?? 0;
                $insertStatus = $this->insertStatus($input['payment_id'], self::STATUS_PENDING);
                $input['payment_status_id'] = $insertStatus['data']['last_id'] ?? 0;
            }
            
            $this->db->commit();
            $this->log->info("Create Payment Success", $input);
            return [
                'success' => true,
                'data' => $input
            ];
        } catch (\PDOException $e) {
            $this->db->rollBack();
            $this->log->error($e->getMessage());
            return [
                'success' => false
            ];
        }
    }

    public function updateStatusPayment($payment_id, $transaction_status)
    {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE {$this->paymentTable} SET transaction_status=:transaction_status WHERE id=:payment_id";

            $statement = $this->db->prepare($sql);

            $data = [
                ':transaction_status' => $transaction_status,
                ':payment_id' => $payment_id
            ];

            $updatePayment = $statement->execute($data);
            if ($updatePayment) {
                $this->insertStatus($payment_id, $transaction_status);
            }

            $this->db->commit();
            
            $this->log->info("Update Payment Success", [
                'payment_id' => $payment_id,
                'transaction_status' => $transaction_status,
                'status_name' => self::statusName($transaction_status)
            ]);

            return [
                'success' => true,
                'data' => [
                    'last_id' => $this->db->lastInsertId()
                ]
            ];
        } catch (\PDOException $e) {
            $this->db->rollBack();
            $this->log->error($e->getMessage());
            return [
                'success' => false
            ];
        }
    }

    public function findInvoiceId($invoice_id)
    {
        try {
            $sql = "SELECT id, invoice_id
                FROM {$this->paymentTable}
                WHERE invoice_id = :invoice_id
            ";

            $statement = $this->db->prepare($sql);

            $statement->bindParam(':invoice_id', $invoice_id);

            $statement->execute();

            $data = $statement->fetch(\PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'data' => $data
            ];
        } catch (\PDOException $e) {
            $this->log->error($e->getMessage());
            return [
                'success' => false
            ];
        }
    }

    public function findPaymentInvoiceStatus($payment_id)
    {
        try {
            $sql = "SELECT id, invoice_id, transaction_status
                FROM {$this->paymentTable}
                WHERE id = :payment_id
            ";

            $statement = $this->db->prepare($sql);

            $statement->bindParam(':payment_id', $payment_id);

            $statement->execute();

            $data = $statement->fetch(\PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'data' => $data
            ];
        } catch (\PDOException $e) {
            $this->log->error($e->getMessage());
            return [
                'success' => false
            ];
        }
    }

    public static function statusName($statusId)
    {
        switch ($statusId) {
            case self::STATUS_PENDING:
                $name = "Pending";
                break;

            case self::STATUS_PAID:
                $name = "Paid";
                break;

            case self::STATUS_FAILED:
                $name = "Failed";
                break;
            
            default:
                $name = "-";
                break;
        }

        return $name;
    }

}
