<?php
declare(strict_types=1);

namespace SelamiConsoleTest;

use Selami\Console;
use Laminas\ServiceManager\ServiceManager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use SelamiConsoleTest;

class ApplicationFactoryTest extends \Codeception\Test\Unit
{
    private $container;

    protected function _before()
    {
    }

    protected function successfulConsoleApp() : void
    {
        $container = new ServiceManager([
            'factories' => [
                SelamiConsoleTest\Service\PrintService::class => SelamiConsoleTest\Factory\PrintServiceFactory::class
            ]
        ]);
        $container->setService(
            'config',
            [
                'greeting' => 'Dear'
            ]
        );
        $container->setService(
            'commands',
            [
                SelamiConsoleTest\Command\GreetingCommand::class
            ]
        );
        $this->container = $container;
    }

    protected function failedConsoleApp() : void
    {
        $container = new ServiceManager([
            'factories' => [
                SelamiConsoleTest\Service\PrintService::class => SelamiConsoleTest\Factory\PrintServiceFactory::class
            ]
        ]);
        $container->setService(
            'config',
            [
                'greeting' => 'Dear'
            ]
        );
        $container->setService(
            'commands',
            [
                SelamiConsoleTest\Command\GreetingCommand::class,
                SelamiConsoleTest\Command\GreetingCommandWithArrayDependency::class,
            ]
        );
        $this->container = $container;
    }
    
    protected function failedForNonExistingServiceConsoleApp() : void
    {
        $container = new ServiceManager([
            'factories' => [
                SelamiConsoleTest\Service\PrintService::class => SelamiConsoleTest\Factory\PrintServiceFactory::class
            ]
        ]);
        $container->setService(
            'config',
            [
                'cache' => '/tmp/uRt48sl'
            ]
        );
        $container->setService(
            'commands',
            [
                SelamiConsoleTest\Command\GreetingCommand::class,
                SelamiConsoleTest\Command\GreetingCommandWithServiceDependency::class,
            ]
        );
        $this->container = $container;
    }

    protected function _after()
    {
    }

    /**
     * @test
     */
    public function shouldRunCommandSuccessfully() : void
    {
        $this->successfulConsoleApp();
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $input = new ArrayInput(['command'=> 'command:greeting', 'name' => 'Kedibey']);
        $output = new BufferedOutput();
        $cli->run($input, $output);
        $return  = $output->fetch();
        $this->assertEquals('Hello Dear Kedibey' . PHP_EOL, $return);
    }

    /**
     * @test
     */
    public function shouldFailNonExistingArrayDependency() : void
    {
        $this->expectException(Console\Exception\DependencyNotFoundException::class);
        $this->failedConsoleApp();
        $input = new ArrayInput(array('command'=>'command:greeting-with-array-dependency', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $output = new BufferedOutput();
        $cli->run($input, $output);
    }
    /**
     * @test
     */
    public function shouldFailNonExistingServiceDependency() : void
    {
        $this->expectException(Console\Exception\DependencyNotFoundException::class);
        $this->failedForNonExistingServiceConsoleApp();
        $input = new ArrayInput(array('command'=>'command:greeting-with-service-dependency', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $output = new BufferedOutput();
        $cli->run($input, $output);
    }
    /**
     * @testw
     */
    public function shouldFailNonExistingSCommandDependency() : void
    {
        $this->expectException(Console\Exception\DependencyNotFoundException::class);

        $this->failedForNonExistingServiceConsoleApp();
        $input = new ArrayInput(array('command'=>'command:greeting-with-service-dependency', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $output = new BufferedOutput();
        $cli->run($input, $output);
    }
}
