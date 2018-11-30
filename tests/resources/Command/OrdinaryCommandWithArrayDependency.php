<?php
declare(strict_types=1);


namespace SelamiConsoleTest\Command;

use SelamiConsoleTest\Service\PrintService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class OrdinaryCommandWithArrayDependency extends Command
{
    /**
     * @var PrintService
     */
    private $printService;
    private $config;

    public function __construct(PrintService $printService, array $args, string $name = null)
    {
        $this->printService = $printService;
        $this->config = $config;
        parent::__construct($name);
    }

    protected function configure() : void
    {
        $this
            ->setName('command:ordinary-with-array-dependency')
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
