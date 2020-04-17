<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use LowlyPHP\Service\Api\FilterInterface;
use LowlyPHP\Service\Api\RepositoryInterface;
use LowlyPHP\Service\Api\RepositorySearchInterface;
use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Console\ConfirmationQuestionFactory;
use Relay\Console\TableFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Repository search command abstraction. Provides most features to search any repository.
 *
 * Extending classes are responsible for customizing methods getTableHeaders and getRecordColumns.
 */
abstract class AbstractSearchCommand extends Command
{
    const FILTER_STRING_DELIMITER = ';';
    const FILTER_PATTERN = '/([aA-zZ0-9_]+)([<>=!~]{1,2})(.+)/';
    const OPTION_FILTER = 'filter';
    const PAGE_SIZE = 25;

    /** @var ConfirmationQuestionFactory */
    protected $confirmationQuestionFactory;

    /** @var RepositorySearchInterface|\Relay\Api\Data\StatisticRepositoryInterface */
    protected $repository;

    /** @var RepositorySearchFactory */
    protected $repositorySearchFactory;

    /** @var TableFactory */
    protected $tableFactory;

    /**
     * @param RepositoryInterface $repository
     * @param RepositorySearchFactory $repositorySearchFactory
     * @param TableFactory $tableFactory
     * @param ConfirmationQuestionFactory $confirmationQuestionFactory
     * @param string|null $name
     */
    public function __construct(
        RepositoryInterface $repository,
        RepositorySearchFactory $repositorySearchFactory,
        TableFactory $tableFactory,
        ConfirmationQuestionFactory $confirmationQuestionFactory,
        ?string $name = null
    ) {
        if (!\method_exists($repository, 'list')) {
            throw new \InvalidArgumentException('Repository must provide a list method.');
        }

        parent::__construct($name);

        $this->repository = $repository;
        $this->repositorySearchFactory = $repositorySearchFactory;
        $this->tableFactory = $tableFactory;
        $this->confirmationQuestionFactory = $confirmationQuestionFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Search records.');
        $this->addOption(
            static::OPTION_FILTER,
            'f',
            InputOption::VALUE_REQUIRED,
            'Filter(s) in format: key=value[;key<=value;...] Supported operators: <, <=, >, >=, !=, =, ~'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $page = 1;
        $total = $this->getTotal();

        if (!$total) {
            $output->writeln('No records in the database.');
            return 1;
        }

        /** @var \Symfony\Component\Console\Helper\Table $table */
        $table = $this->tableFactory->create($output);
        $table->setHeaders($this->getTableHeaders());

        $output->writeln('Search Results');

        do {
            $table->setRows([]);

            /** @var \LowlyPHP\Service\Resource\EntityInterface[] $results */
            $results = $this->repository->list($this->getCriteria($input, $page));

            if ($page === 1 && empty($results)) {
                $output->writeln('No records found matching your query.');
                return 1;
            }

            /** @var \LowlyPHP\Service\Resource\EntityInterface $record */
            foreach ($results as $record) {
                $table->addRow($this->getRecordColumns($record));
            }

            $table->render();
            $output->writeln(\sprintf('Page %d of %d', $page, \ceil($total / static::PAGE_SIZE)));

            if (($page * static::PAGE_SIZE) < $total
                && $this->requestMore($input, $output)
            ) {
                $page++;
            } else {
                break;
            }

        } while (!empty($results));

        return 0;
    }

    /**
     * Prepare the repository search criteria from the console input.
     *
     * @param InputInterface $input
     * @param int $page
     * @return RepositorySearchInterface
     * @throws \LowlyPHP\Exception\ConfigException
     */
    protected function getCriteria(InputInterface $input, int $page = 1) : RepositorySearchInterface
    {
        /** @var RepositorySearchInterface $criteria */
        $criteria = $this->repositorySearchFactory->create();
        $criteria->setPage($page);
        $criteria->setLimit(static::PAGE_SIZE);

        /** @var array $filters */
        $filters = \explode(static::FILTER_STRING_DELIMITER, $input->getOption(static::OPTION_FILTER));

        \array_walk(
            $filters,
            function ($filter) use ($criteria) {
                \preg_match(static::FILTER_PATTERN, $filter, $components);

                if (\count($components) !== 4) {
                    throw new \InvalidArgumentException('Filter input is invalid.');
                }

                if ($components[2] === '~') {
                    $components[2] = FilterInterface::COMPARATOR_LIKE;
                }

                $criteria->addFilter($components[1], $components[3], $components[2]);
            }
        );

        return $criteria;
    }

    /**
     * Get the total number of records.
     *
     * @return int
     * @throws \LowlyPHP\Exception\ConfigException
     */
    protected function getTotal() : int
    {
        return $this->repository->stat($this->repositorySearchFactory->create())->getTotal();
    }

    /**
     * Prompt the user to access additional results.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     * @throws \LowlyPHP\Exception\ConfigException
     */
    protected function requestMore(InputInterface $input, OutputInterface $output) : bool
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $question = $this->confirmationQuestionFactory->create('Show more results?');

        return $helper->ask($input, $output, $question);
    }

    /**
     * Get the table header definition.
     *
     * @return array
     */
    protected function getTableHeaders() : array
    {
        return ['ID'];
    }

    /**
     * Retrieve values from the given record according to the table header order.
     *
     * @param EntityInterface $record
     * @return array
     */
    protected function getRecordColumns(EntityInterface $record) : array
    {
        return [$record->getEntityId()];
    }
}
