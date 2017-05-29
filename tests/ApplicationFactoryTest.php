<?php
declare(strict_types=1);

namespace tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Selami\Console;
use Zend\ServiceManager\ServiceManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Helper\HelperSet;
use Selami\Console\ContainerHelper;
use stdClass;
use SelamiConsoleTest;
use InvalidArgumentException;

class MySelamiConsoleLibrary extends TestCase
{
    protected $container;

    public function setUp() : void
    {
        $container = new ServiceManager();
        $container->setService(
            'commands', [
                SelamiConsoleTest\Command\OrdinaryCommand::class
            ]
        );
        $container->setService('config', ['lang' => 'English']);
        $this->container = $container;
    }

    /**
     * @test
     */
    public function shouldReturnHelperSetSuccessfully() : void
    {
        $helperSet = new HelperSet([ContainerInterface::class => new ContainerHelper($this->container)]);
        $helper = $helperSet->get(ContainerInterface::class);
        $this->assertEquals('containerHelper', $helper->getName());
        $container = $helper->getContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
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
        $this->assertEquals('Hello world in English', $return);
    }


}
