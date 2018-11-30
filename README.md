# Selami Console

Symfony Console application factory that autowires dependecies. Use any PSR-11 compatible DIC library.


## Installation

```bash
composer install selami/console
```

## Usage

Assume we use Zend ServiceManager as our PSR-11 compatible DIC.

### 1. Create your service

```php
<?php
declare(strict_types=1);

namespace MyConsoleApplication\Service;

class PrintService
{
    public function formatMessage(string $message) : string
    {
        return 'Hello ' . $message;
    }
}
```

### 2. Create a factory for your service

```php
<?php
declare(strict_types=1);

namespace MyConsoleApplication\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use MyConsoleApplication\Service\PrintService;

class PrintServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : PrintService
    {
        return new PrintService();
    }
}

```

### 3. Create your Symfony Command

```php
<?php
declare(strict_types=1);

namespace MyConsoleApplication\Command;

use MyConsoleApplication\Service\PrintService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class OrdinaryCommand extends Command
{
    /**
     * @var PrintService
     */
    private $printService;
    private $config;

    public function __construct(PrintService $printService, array $config, string $name = null)
    {
        $this->printService = $printService;
        $this->config = $config;
        parent::__construct($name);
    }

    protected function configure() : void
    {
        $this
            ->setName('command:ordinary')
            ->setDescription('Prints "Hello {config.greeting} {name}')
            ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $name = $input->getArgument('name');
        $output->writeln($this->printService->formatMessage($name) . ' -'. $this->config['cache']);
    }
}
```



### 5.Create your executable console application (i.e. ./bin/console)


```php
#!/usr/bin/env php

<?php
declare(strict_types=1);

require_once ('path/to/vendor/autoload.php');

use Selami\Console\ApplicationFactory;
use Zend\ServiceManager\ServiceManager;

$container = new ServiceManager(
	[
    	'factories' => [
        	SelamiConsoleTest\Service\PrintService::class => SelamiConsoleTest\Factory\PrintServiceFactory::class
    	]
	]
);
$container->setService(
    'commands', [
        SelamiConsoleTest\Command\OrdinaryCommand::class
    ]
);
$container->setService('config', ['greeting' => 'Dear']);

$cli = ApplicationFactory::makeApplication($container);
$cli->run();
```

### 6. Run your command on terminal

```bash
./bin/console command:ordinary Kedibey
```
Outputs: Hello Dear Kedibey
