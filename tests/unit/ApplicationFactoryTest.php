<?php
declare(strict_types=1);

namespace SelamiConsoleTest;

use Selami\Console;
use Zend\ServiceManager\ServiceManager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use SelamiConsoleTest;

class ApplicationFactoryTest extends \Codeception\Test\Unit
{
    private $container;

    protected function _before()
    {
    }

    protected function successfulConsoleApp()
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
                SelamiConsoleTest\Command\OrdinaryCommand::class
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
                'cache' => '/tmp/uRt48sl'
            ]
        );
        $container->setService(
            'commands',
            [
                SelamiConsoleTest\Command\OrdinaryCommand::class,
                SelamiConsoleTest\Command\OrdinaryCommandWithArrayDependency::class,
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
                SelamiConsoleTest\Command\OrdinaryCommand::class,
                SelamiConsoleTest\Command\OrdinaryCommandWithServiceDependency::class,
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
        $input = new ArrayInput(array('command'=>'command:ordinary', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $output = new BufferedOutput();
        $cli->run($input, $output);
        $return  = $output->fetch();
        $this->assertEquals('Hello world -/tmp/uRt48sl' . PHP_EOL, $return);
    }

    /**
     * @test
     * @expectedException \Selami\Console\Exception\DependencyNotFoundException
     */
    public function shouldFailNonExistingArrayDependency() : void
    {
        $this->failedConsoleApp();
        $input = new ArrayInput(array('command'=>'command:ordinary-with-array-dependency', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $output = new BufferedOutput();
        $cli->run($input, $output);
    }
    /**
     * @test
     * @expectedException \Selami\Console\Exception\DependencyNotFoundException
     */
    public function shouldFailNonExistingServiceDependency() : void
    {
        $this->failedForNonExistingServiceConsoleApp();
        $input = new ArrayInput(array('command'=>'command:ordinary-with-service-dependency', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $output = new BufferedOutput();
        $cli->run($input, $output);
    }
    /**
     * @testw
     * @expectedExfception \Selami\Console\Exception\DependencyNotFoundException
     */
    public function shouldFailNonExistingSCommandDependency() : void
    {
        $this->failedForNonExistingServiceConsoleApp();
        $input = new ArrayInput(array('command'=>'command:ordinary-with-service-dependency', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $output = new BufferedOutput();
        $cli->run($input, $output);
    }
}