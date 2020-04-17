<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Data;

/**
 * An aggregate record which represents multiple entities. Used to convey statistics about a collection. Read-only.
 */
interface StatisticEntityInterface
{
    /**
     * Get the IDs of all entities in the statistic set.
     *
     * @return int[]
     */
    public function getIds() : array;

    /**
     * Get the total number of records.
     *
     * @return int
     */
    public function getTotal() : int;
}
