<?php
declare(strict_types=1);


namespace SelamiConsoleTest\Command;

use SelamiTest\Service\PrintService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class OrdinaryCommand extends Command
{

    public function __construct(PrintService $printService, string $name = null)
    {
        $this->printService=$printService;

        parent::__construct($name);
    }

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
        $name = $input->getArgument('name');
        $output->write('Hello '.$name);
    }
}
