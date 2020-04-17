<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use Relay\Api\Data\StatisticRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Repository stat command abstraction. Provides a repeatable pattern for getting repository statististics.
 *
 * Extending classes are responsible for customizing methods getTableHeaders and getRecordColumns.
 */
abstract class AbstractStatCommand extends Command
{
    const OPTION_SHOW_IDS = 'show-ids';
    const MAX_IDS_SHOWN = 500;

    /** @var StatisticRepositoryInterface */
    protected $repository;

    /** @var RepositorySearchFactory */
    protected $repositorySearchFactory;

    /**
     * @param StatisticRepositoryInterface $repository
     * @param RepositorySearchFactory $repositorySearchFactory
     * @param string|null $name
     */
    public function __construct(
        StatisticRepositoryInterface $repository,
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
        $this->setDescription('Show repository statistics.');
        $this->addOption(
            static::OPTION_SHOW_IDS,
            'i',
            InputOption::VALUE_NONE,
            'Show record IDs'
        );
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Relay\Api\Data\StatisticEntityInterface $stats */
        $stats = $this->repository->stat($this->repositorySearchFactory->create());
        /** @var int $total */
        $total = $stats->getTotal();

        $output->writeln(\sprintf('Total records: %d', $total));

        if ($input->getOption(static::OPTION_SHOW_IDS)) {
            $ids = \array_slice($stats->getIds(), 0, static::MAX_IDS_SHOWN);
            $output->writeln(
                \sprintf(
                    'IDs: %s%s',
                    \implode(', ', $ids),
                    $total > \count($ids) ? \sprintf('… (%s more)', $total - \count($ids)) : ''
                )
            );
        }

        return !($total > 0);
    }
}
