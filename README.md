# Selami Console

Symfony Console application factory that autowires dependecies. Use any PSR-11 compatible DIC library.

[![Build Status](https://api.travis-ci.org/selamiphp/console.svg?branch=master)](https://travis-ci.org/selamiphp/console) [![Coverage Status](https://coveralls.io/repos/github/selamiphp/console/badge.svg?branch=master)](https://coveralls.io/github/selamiphp/console?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/selamiphp/console/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/selamiphp/console/) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/d564565dbc754376a9d022731ec1af75)](https://www.codacy.com/app/mehmet/console?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=selamiphp/console&amp;utm_campaign=Badge_Grade) [![Latest Stable Version](https://poser.pugx.org/selami/console/v/stable)](https://packagist.org/packages/selami/console) [![Total Downloads](https://poser.pugx.org/selami/console/downloads)](https://packagist.org/packages/selami/console) [![Latest Unstable Version](https://poser.pugx.org/selami/console/v/unstable)](https://packagist.org/packages/selami/console) [![License](https://poser.pugx.org/selami/console/license)](https://packagist.org/packages/selami/console)


## Installation

```bash
composer require selami/console
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
    public function formatMessage(string $greeting, string $message) : string
    {
        return 'Hello ' . $greeting . ' ' . $message;
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

class GreetingCommand extends Command
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
            ->setName('command:greeting')
            ->setDescription('Prints "Hello {config.greeting} {name}')
            ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $name = $input->getArgument('name');
        $output->writeln($this->printService->formatMessage($this->config['greeting'], $name));
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
use Laminas\ServiceManager\ServiceManager;

$container = new ServiceManager(
	[
    	'factories' => [
        	MyConsoleApplication\Service\PrintService::class => MyConsoleApplication\Factory\PrintServiceFactory::class
    	]
	]
);
$container->setService(
    'commands', [
        MyConsoleApplication\Command\GreetingCommand::class
    ]
);
$container->setService('config', ['greeting' => 'Dear']);

$cli = ApplicationFactory::makeApplication($container);
$cli->run();
```

### 6. Run your command on terminal

```bash
./bin/console command:greeting Kedibey
```
Prints: Hello Dear Kedibey
