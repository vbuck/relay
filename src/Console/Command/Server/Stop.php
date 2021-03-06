<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Server;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Stop extends Command
{
    /** @var ServiceLocator */
    private $serviceLocator;

    /**
     * @param ServiceLocator $serviceLocator
     * @param string|null $name
     */
    public function __construct(ServiceLocator $serviceLocator, ?string $name = null)
    {
        parent::__construct($name);

        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Stop the relay web service.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = \sprintf('%s stop', $this->serviceLocator->locate());
        \trim(\exec($command, $result, $code));
        $output->writeln($result);

        return $code;
    }
}
