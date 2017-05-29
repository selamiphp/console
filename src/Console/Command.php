<?php
declare(strict_types=1);

namespace Selami\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Psr\Container\ContainerInterface;

class Command extends SymfonyCommand
{
    /**
     * ContainerInterface
     * @var
     */
    private $container;

    public function __construct(?string $name)
    {
        parent::__construct($name);
        $this->setContainer();
    }
    private function setContainer() : void
    {
        $this->container = $this->getHelper('container');
    }
}