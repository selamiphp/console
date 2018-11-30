<?php
declare(strict_types=1);

namespace Selami\Console;

use Symfony\Component\Console\Application;
use Psr\Container\ContainerInterface;
use Selami\Stdlib\Resolver;
use ReflectionClass;
use ReflectionException;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;

class ApplicationFactory
{
    public static function makeApplication(
        ContainerInterface $container,
        string $name = 'ConsoleApp',
        string $version = '0.0.1'
    ) : Application {
        /**
         * @var  array $commands
         */
        $commands = $container->get('commands');
        $cli = new Application($name, $version);
        $consoleApplicationArguments = [
            'name' => $name,
            'version' => $version
        ];
        foreach ($commands as $command) {
            $controllerConstructorArguments = Resolver::getParameterHints($command, '__construct');
            $arguments = [];
            foreach ($controllerConstructorArguments as $argumentName => $argumentType) {
                $arguments[] = self::getArgument(
                    $container,
                    $consoleApplicationArguments,
                    $argumentName,
                    $argumentType
                );
            }
            try {
                $reflectionClass = new ReflectionClass($command);
            } catch (ReflectionException $exception) {
                throw new InvalidArgumentException($exception->getMessage());
            }
            /**
             * @var Command $autoWiredCommand
             */
            $autoWiredCommand = $reflectionClass->newInstanceArgs($arguments);
            $cli->add($autoWiredCommand);
        }
        return $cli;
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
            throw new InvalidArgumentException(
                sprintf('Container does not have item for array: %s', $argumentName)
            );
        }
        if ($argumentType === Resolver::ARRAY) {
            return $container->get($argumentName);
        }
        if ($container->has($argumentType) === false) {
            throw new InvalidArgumentException(
                sprintf('Container does not have item for service: %s', $argumentType)
            );
        }
        return $container->get($argumentType);
    }
}
