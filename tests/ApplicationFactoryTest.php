<?php namespace SelamiConsoleTest;

use Selami\Console;
use Zend\ServiceManager\ServiceManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use SelamiConsoleTest;

class ApplicationFactoryTest extends \Codeception\Test\Unit
{
    private $container;
    protected function _before()
    {
        $container = new ServiceManager();
        $container->setService(
            'commands',
            [
                SelamiConsoleTest\Command\OrdinaryCommand::class
            ]
        );
        $container->setService(
            'dependencies',
            [
                SelamiConsoleTest\Service\PrintService::class => SelamiConsoleTest\Service\PrintService::class
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
    public function shouldReturnConsoleApplicationSuccessfully() : void
    {

        $input = new ArrayInput(array('command'=>'command:ordinary', 'name' => 'world'));
        $cli = Console\ApplicationFactory::makeApplication($this->container, 'TestApp', '1.0.1');
        $cli->setAutoExit(false);
        $this->assertInstanceOf(Application::class, $cli);
        $this->assertEquals('TestApp', $cli->getName());
        $this->assertEquals('1.0.1', $cli->getVersion());
        $fp = tmpfile();
        $output = new StreamOutput($fp);

        $cli->run($input, $output);
        fseek($fp, 0);
        $return  = '';
        while (!feof($fp)) {
            $return = fread($fp, 4096);
        }
        fclose($fp);
        $this->assertEquals('Hello world', $return);
    }
}
