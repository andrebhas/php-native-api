<?php

require_once __DIR__ . '/../vendor/autoload.php';


use Dotenv\Dotenv;
use Src\System\Database\DatabaseConnector;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbConnection = (new DatabaseConnector())->getConnection();

