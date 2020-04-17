<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Action;

use Relay\Data\Action;
use Relay\Data\ActionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    const ARGUMENT_DESCRIPTION = 'description';
    const ARGUMENT_TYPE = 'type';
    const ARGUMENT_PARAMETERS = 'parameters';

    /** @var ActionRepository */
    private $actionRepository;

    /**
     * @param ActionRepository $actionRepository
     * @param string|null $name
     */
    public function __construct(ActionRepository $actionRepository, ?string $name = null)
    {
        parent::__construct($name);

        $this->actionRepository = $actionRepository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Create a new action.');
        $this->addArgument(
            static::ARGUMENT_TYPE,
            InputArgument::REQUIRED,
            'Action type'
        );
        $this->addArgument(
            static::ARGUMENT_DESCRIPTION,
            InputArgument::OPTIONAL,
            'Action description'
        );
        $this->addArgument(
            static::ARGUMENT_PARAMETERS,
            InputArgument::OPTIONAL,
            'Action parameters in format: key1=value1&key2=value2&...'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Action $action */
        $action = $this->actionRepository->create(
            [
                Action::TYPE => $input->getArgument(static::ARGUMENT_TYPE),
                Action::DESCRIPTION => $input->getArgument(static::ARGUMENT_DESCRIPTION),
                Action::PARAMETERS => $this->parseParameters(
                    (string) $input->getArgument(static::ARGUMENT_PARAMETERS)
                ),
            ]
        );

        if ($action->getEntityId() > 0) {
            $output->writeln(\sprintf('Created action #%d.', $action->getEntityId()));
            return 0;
        }

        $output->writeln('Failed to create action');
        return 1;
    }

    /**
     * Parse parameters from the given input string.
     *
     * Expects key-value pairs in format: key1=value1&key2=value2&...
     *
     * @param string $input
     * @return array
     */
    private function parseParameters(string $input = '') : array
    {
        $result = [];
        \parse_str($input, $result);

        return $result;
    }
}
