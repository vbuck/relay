<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Data;

use LowlyPHP\Service\Api\RepositorySearchInterface;

/**
 * A data service contract which provides statistics about entities. Used for efficient queries of large data sets.
 */
interface StatisticRepositoryInterface
{
    /**
     * Query the repository for aggregate info about entities.
     *
     * @param RepositorySearchInterface $criteria
     * @return StatisticEntityInterface
     */
    public function stat(RepositorySearchInterface $criteria) : StatisticEntityInterface;
}
