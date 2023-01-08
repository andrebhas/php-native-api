<?php
require_once __DIR__ . "/../bootstrap.php";

/*
|--------------------------------------------------------------------------
| Create payments table & payment_status table
*/
$statement = <<<EOS
    CREATE TABLE IF NOT EXISTS payments (
        id INT NOT NULL AUTO_INCREMENT,
        invoice_id VARCHAR(100) NOT NULL,
        item_name VARCHAR(100) NOT NULL,
        amount DECIMAL(15,2) DEFAULT '0',
        payment_type TINYINT(1) DEFAULT NULL COMMENT "1: virtual_account, 2: credit_card",
        customer_name VARCHAR(100) NOT NULL,
        merchant_id VARCHAR(100) NOT NULL,
        transaction_status TINYINT(1) NOT NULL DEFAULT 0 COMMENT "0: Pending, 1: Paid, 2: Failed",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=INNODB;

    CREATE TABLE IF NOT EXISTS payment_status (
        id INT NOT NULL AUTO_INCREMENT,
        payment_id INT NOT NULL,
        status TINYINT(1) NOT NULL DEFAULT 0 COMMENT "0: Pending, 1: Paid, 2: Failed",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY `payment_id_id_foreign` (`payment_id`),
        CONSTRAINT `payment_id_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=INNODB;

EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Migration Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}


