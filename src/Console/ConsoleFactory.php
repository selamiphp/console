<?php
declare(strict_types=1);

namespace Selami\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Psr\Container\ContainerInterface;


class ConsoleFactory
{
    static public function createApplication(ContainerInterface $container, ?string $name, ?string $version) : Application
    {
        $commands = $container->get('commands');
        $cli = new Application($name, $version);
        $cli->setCatchExceptions(true);
        $cli->setHelperSet(new HelperSet(['container' => $container]));
        $cli->addCommands($commands);
        return $cli;
    }

}