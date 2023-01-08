<?php

namespace Src\System\Database;

use Src\System\Logger\Log;

class DatabaseConnector
{
    private $dbConnection = null;
    private $log;

    public function __construct()
    {
        $this->log = (new Log('system'))->run();

        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db   = $_ENV['DB_DATABASE'];
        $user = $_ENV['DB_USERNAME'];
        $pass = $_ENV['DB_PASSWORD'];

        $mysql = "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db";

        try {
            $this->dbConnection = new \PDO($mysql, $user, $pass);
            $this->dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
            $this->log->error($e->getMessage());
            exit();

        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}
