<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Setup;

use LowlyPHP\ApplicationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Install extends Command
{
    /** @var ApplicationManager */
    private $app;

    /** @var ConnectionWrapperFactory */
    private $connectionWrapperFactory;

    /**
     * @param ConnectionWrapperFactory $connectionWrapperFactory
     * @param ApplicationManager|null $app
     * @param string|null $name
     */
    public function __construct(
        ConnectionWrapperFactory $connectionWrapperFactory,
        ApplicationManager $app = null,
        ?string $name = null
    ) {
        parent::__construct($name);

        $this->app = $app ?? ApplicationManager::getInstance();
        $this->connectionWrapperFactory = $connectionWrapperFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Install and configure Relay.');
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connections = \array_keys((array) $this->app->config('connections'));

        foreach ($connections as $name) {
            /** @var ConnectionWrapper $wrapper */
            $wrapper = $this->connectionWrapperFactory->create($name);
            /** @var \PDO $connection */
            $connection = $wrapper->getConnection();
            /** @var \PDOStatement $statement */
            $statement = $connection->prepare(
                \sprintf('CREATE DATABASE IF NOT EXISTS `%s`', $wrapper->getDatabase())
            );

            if (!$statement->execute()) {
                $output->writeln(\sprintf('Failed to create database "%s."', $wrapper->getDatabase()));
                return 1;
            }
        }

        $output->writeln('Installation complete.');

        return 0;
    }
}
