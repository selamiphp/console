<?php
declare(strict_types=1);

namespace Selami\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Psr\Container\ContainerInterface;


class ApplicationFactory
{
    static public function makeApplication(ContainerInterface $container, ?string $name, ?string $version) : Application
    {
        /**
         * @var  array $commands
         */
        $commands = $container->get('commands');
        $helperSet = new HelperSet([ContainerInterface::class => new ContainerHelper($container)]);
        $cli = new Application($name, $version);
        $cli->setCatchExceptions(true);
        $cli->setHelperSet($helperSet);
        foreach ($commands as $command) {
            $commandInstance = new $command();
           // $commandInstance->setHelperSet($helperSet);
            $cli->add($commandInstance);
        }
        return $cli;
    }

}