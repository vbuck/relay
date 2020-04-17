<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use LowlyPHP\Service\Api\RepositoryInterface;
use LowlyPHP\Service\Resource\EntityInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Repository delete command abstraction. Provides a repeatable pattern for deleting entities.
 */
abstract class AbstractInfoCommand extends Command
{
    const ARGUMENT_ID = 'id';

    /** @var RepositoryInterface */
    protected $repository;

    /** @var RepositorySearchFactory */
    protected $repositorySearchFactory;

    /**
     * @param RepositoryInterface $repository
     * @param RepositorySearchFactory $repositorySearchFactory
     * @param string|null $name
     */
    public function __construct(
        RepositoryInterface $repository,
        RepositorySearchFactory $repositorySearchFactory,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->repository = $repository;
        $this->repositorySearchFactory = $repositorySearchFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Show record details.');
        $this->addArgument(
            static::ARGUMENT_ID,
            InputArgument::REQUIRED,
            'Record ID'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \LowlyPHP\Service\Api\RepositorySearchInterface $criteria */
        $criteria = $this->repositorySearchFactory->create();
        $criteria->addFilter(EntityInterface::ID, $input->getArgument(static::ARGUMENT_ID));

        /** @var \LowlyPHP\Service\Resource\EntityInterface $record */
        $record = \current($this->repository->list($criteria));

        if (!$record) {
            $output->writeln('Record does not exist.');
            return 1;
        }

        $output->writeln(\json_encode($record->export(), JSON_PRETTY_PRINT));

        return 0;
    }
}
