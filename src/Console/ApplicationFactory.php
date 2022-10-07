<?php
declare(strict_types=1);

namespace Selami\Console;

use Symfony\Component\Console\Application;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Selami\Stdlib\Resolver;
use ReflectionClass;
use Selami\Console\Exception\DependencyNotFoundException;
use Selami\Stdlib\Exception\ClassOrMethodCouldNotBeFound;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;

class ApplicationFactory
{
    public static function makeApplication(
        ContainerInterface $container,
        string $name = 'ConsoleApp',
        string $version = '0.0.1'
    ) : Application {
        /**
         * @var array $commands
         */
        $commands = $container->get('commands');
        $cli = new Application($name, $version);
        $consoleApplicationArguments = [
            'name' => $name,
            'version' => $version
        ];
        foreach ($commands as $command) {
            $controllerConstructorArguments = self::getControllerConstructorArguments($command);
            $arguments = [];
            foreach ($controllerConstructorArguments as $argumentName => $argumentType) {
                $arguments[] = self::getArgument(
                    $container,
                    $consoleApplicationArguments,
                    $argumentName,
                    $argumentType
                );
            }
            $reflectionClass = new ReflectionClass($command);
            /**
             * @var Command $autoWiredCommand
             */
            $autoWiredCommand = $reflectionClass->newInstanceArgs($arguments);
            $cli->add($autoWiredCommand);
        }
        return $cli;
    }

    private static function getControllerConstructorArguments($command): ?array
    {
        try {
            return Resolver::getParameterHints($command, '__construct');
        } catch (ClassOrMethodCouldNotBeFound|ServiceNotFoundException $e) {
            throw new DependencyNotFoundException(
                sprintf(
                    '%s when calling command: %s',
                    $e->getMessage(),
                    $command
                )
            );
        }
    }

    private static function getArgument(
        ContainerInterface $container,
        array $consoleApplicationArguments,
        string $argumentName,
        string $argumentType
    ) {

        if (array_key_exists($argumentName, $consoleApplicationArguments)) {
            return $consoleApplicationArguments[$argumentName];
        }
        if ($argumentType === Resolver::ARRAY && $container->has($argumentName) === false) {
            throw new DependencyNotFoundException(
                sprintf('Container does not have an item for array: %s', $argumentName)
            );
        }
        if ($argumentType === Resolver::ARRAY) {
            return $container->get($argumentName);
        }
        try {
            return $container->get($argumentType);
        } catch (ServiceNotFoundException $exception) {
            throw new DependencyNotFoundException(
                sprintf('Container does not have an item service: %s', $argumentType)
            );
        }
    }
}
