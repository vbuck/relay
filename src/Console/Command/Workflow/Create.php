<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Workflow;

use Relay\Data\Workflow;
use Relay\Data\WorkflowRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends Command
{
    const ARGUMENT_NAME = 'name';
    const ARGUMENT_DESCRIPTION = 'description';
    const ARGUMENT_STATUS = 'status';
    const ARGUMENT_TYPE_ID = 'type-id';
    const ARGUMENT_WORKFLOW_ID = 'workflow-id';

    /** @var WorkflowRepository */
    private $workflowRepository;

    /**
     * @param WorkflowRepository $workflowRepository
     * @param string|null $name
     */
    public function __construct(WorkflowRepository $workflowRepository, ?string $name = null)
    {
        parent::__construct($name);

        $this->workflowRepository = $workflowRepository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Create a new workflow.');
        $this->addArgument(
            static::ARGUMENT_NAME,
            InputArgument::REQUIRED,
            'Workflow name'
        );
        $this->addArgument(
            static::ARGUMENT_DESCRIPTION,
            InputArgument::OPTIONAL,
            'Workflow description'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // @todo Launch action guide

        /** @var Workflow $workflow */
        $workflow = $this->workflowRepository->create(
            [
                Workflow::NAME => $input->getArgument(static::ARGUMENT_NAME),
                Workflow::DESCRIPTION => $input->getArgument(static::ARGUMENT_DESCRIPTION),
            ]
        );

        if ($workflow->getEntityId() > 0) {
            $output->writeln(\sprintf('Created workflow #%d.', $workflow->getEntityId()));
            return 0;
        }

        $output->writeln('Failed to create workflow');
        return 1;
    }
}
