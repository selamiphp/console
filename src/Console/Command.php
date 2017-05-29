<?php
declare(strict_types=1);

namespace Selami\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Psr\Container\ContainerInterface;

class Command extends SymfonyCommand
{
    protected $container;

    public function __construct(string $name=null)
    {
        parent::__construct($name);
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $helperSet = $this->getApplication()->getHelperSet();
        $helper = $helperSet->get(ContainerInterface::class);
        $this->container =  $helper->getContainer();
    }
}