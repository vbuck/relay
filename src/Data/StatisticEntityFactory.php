<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Data;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Relay\Api\Data\StatisticEntityInterface;

/**
 * Factory for {@see StatisticEntity} instances.
 */
class StatisticEntityFactory
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
     * Create a new statistic object.
     *
     * @param int[] $ids
     * @return StatisticEntityInterface
     * @throws ConfigException
     */
    public function create(array $ids = []) : StatisticEntityInterface
    {
        return $this->app->createObject(StatisticEntityInterface::class, ['ids' => $ids]);
    }
}
