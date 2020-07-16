<?php
declare(strict_types=1);


namespace SelamiConsoleTest\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class GreetingCommandWithServiceDependency extends Command
{
    /**
     * @var NonExistingPrintService
     */
    private $printService;
    private $config;

    public function __construct(NonExistingPrintService $printService, array $config, string $name = null)
    {
        $this->printService = $printService;
        $this->config = $config;
        parent::__construct($name);
    }

    protected function configure() : void
    {
        $this
            ->setName('command:ordinary-with-service-dependency')
            ->setDescription('Show basic information about all mapped entities')
            ->setDefinition([
                new InputArgument('name', InputArgument::REQUIRED),
            ]);
    }


    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $name = $input->getArgument('name');
        $output->write($this->printService->formatMessage($name) . ' -'. $this->config['cache']);
    }
}
