<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\SystemVariable;

use Relay\Data\SystemVariable;
use Relay\Data\SystemVariableRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    const ARGUMENT_NAME = 'name';
    const ARGUMENT_VALUE = 'value';

    /** @var SystemVariableRepository */
    private $systemVariableRepository;

    /**
     * @param SystemVariableRepository $systemVariableRepository
     * @param string|null $name
     */
    public function __construct(SystemVariableRepository $systemVariableRepository, ?string $name = null)
    {
        parent::__construct($name);

        $this->systemVariableRepository = $systemVariableRepository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Create a new system variable.');
        $this->addArgument(
            static::ARGUMENT_NAME,
            InputArgument::REQUIRED,
            'Variable name'
        );
        $this->addArgument(
            static::ARGUMENT_VALUE,
            InputArgument::REQUIRED,
            'Variable value'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SystemVariable $systemVariable */
        $systemVariable = $this->systemVariableRepository->create(
            [
                SystemVariable::NAME => $input->getArgument(static::ARGUMENT_NAME),
                SystemVariable::VALUE => $input->getArgument(static::ARGUMENT_VALUE),
            ]
        );

        if ($systemVariable->getEntityId() > 0) {
            $output->writeln(\sprintf('Created variable #%d.', $systemVariable->getEntityId()));
            return 0;
        }

        $output->writeln('Failed to create variable');
        return 1;
    }
}
