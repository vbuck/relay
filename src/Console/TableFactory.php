<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Factory for {@see Table} instances.
 */
class TableFactory
{
    /** @var ApplicationManager */
    private $app;

    /**
     * @param ApplicationManager|null $app
     * @codeCoverageIgnore
     */
    public function __construct(ApplicationManager $app = null)
    {
        $this->app = $app ?? ApplicationManager::getInstance();
    }

    /**
     * Create a new table object.
     *
     * @param OutputInterface|null $output
     * @return Table
     * @throws ConfigException
     */
    public function create(OutputInterface $output = null) : Table
    {
        return $this->app->createObject(Table::class, ['output' => $output]);
    }
}
