<?php
declare(strict_types=1);


namespace Selami\Console;

use Symfony\Component\Console\Helper\Helper;
use Psr\Container\ContainerInterface;

class ContainerHelper extends Helper
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer() : ContainerInterface
    {
        return $this->container;
    }

    public function getName() : string
    {
        return 'containerHelper';
    }
}