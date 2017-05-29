<?php
declare(strict_types=1);


namespace SelamiConsoleTest\Command;

use Selami\Console\Command as SelamiCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class OrdinaryCommand extends SelamiCommand
{
    protected function configure() : void
    {
        $this
            ->setName('command:ordinary')
            ->setDescription('Show basic information about all mapped entities')
            ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED),
            ]);
    }


    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $config = $this->container->get('config');
        $name = $input->getArgument('name');
        $output->write('Hello ' . $name. ' in ' . $config['lang']);
    }

}