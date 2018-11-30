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
        $container = new ServiceManager(
            [
                'factories' => [
                        SelamiConsoleTest\Service\PrintService::class => SelamiConsoleTest\Factory\PrintServiceFactory::class
                ]
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

    protected function _after()
    {
    }

    /**
     * @test
     */
    public function shouldRunCommandSuccessfully() : void
    {
        $input = new ArrayInput(array('command'=>'command:ordinary', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $output = new BufferedOutput();
        $cli->run($input, $output);
        $return  = $output->fetch();
        $this->assertEquals('Hello world', $return);
    }
}
