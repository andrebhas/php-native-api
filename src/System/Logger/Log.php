<?php
namespace Src\System\Logger;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;

class Log
{
    private $logger;

    public function __construct($channel)
    {
        $this->logger = new Logger($channel);

        $handler = new RotatingFileHandler(__DIR__ . "/../../logs/" . $channel . ".log", 90, Logger::DEBUG);
        $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
        $formatter = new LineFormatter($output, "Y-m-d H:i:s", true, true);
        $handler->setFormatter($formatter);

        $this->logger->pushHandler($handler);
    }

    public function run()
    {
        return $this->logger;
    }
}
