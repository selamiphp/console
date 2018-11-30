<?php
declare(strict_types=1);

namespace SelamiConsoleTest\Service;

class PrintService
{
    public function __construct()
    {
    }

    public function formatMessage(string $message) : string
    {
        return 'Hello ' . $message;
    }
}
