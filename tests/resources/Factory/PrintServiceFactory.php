<?php
declare(strict_types=1);

namespace SelamiConsoleTest\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use SelamiConsoleTest\Service\PrintService;

class PrintServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : PrintService
    {
        return new PrintService();
    }
}
