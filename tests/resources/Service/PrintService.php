<?php
declare(strict_types=1);

namespace SelamiConsoleTest\Service;

class PrintService
{
    public function __construct()
    {
    }

    public function print(string $message) : void
    {
        echo $message . PHP_EOL;
    }
}
