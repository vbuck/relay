<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Tracer;

use Relay\TracerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends Command
{
    /** @var TracerFactory */
    private $tracerFactory;

    /**
     * @param TracerFactory $tracerFactory
     * @param string|null $name
     */
    public function __construct(TracerFactory $tracerFactory, ?string $name = null)
    {
        parent::__construct($name);

        $this->tracerFactory = $tracerFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Start the tracer service.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Relay\Api\TracerInterface $tracer */
        $tracer = $this->tracerFactory->get();

        return 0;
    }
}
