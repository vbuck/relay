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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Repository delete command abstraction. Provides a repeatable pattern for deleting entities.
 */
abstract class AbstractDeleteCommand extends Command
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
    public function __construct(RepositoryInterface $repository, ?string $name = null) {
        parent::__construct($name);

        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Delete record.');
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
        try {
            /** @var \LowlyPHP\Service\Resource\EntityInterface $record */
            $record = $this->repository->read($input->getArgument(static::ARGUMENT_ID));
            $this->repository->delete($record);

            $output->writeln(\sprintf('Deleted record #%d', $record->getEntityId()));
            $output->writeln(\json_encode($record->export(), JSON_PRETTY_PRINT));
        } catch (\Exception $error) {
            $output->writeln(\sprintf('Failed to delete record: %s', $error->getMessage()));
            return 1;
        }

        return 0;
    }
}
