<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Data;

use Relay\Api\Data\StatisticEntityInterface;

/**
 * Implementation for {@see StatisticEntityInterface}.
 */
class StatisticEntity implements StatisticEntityInterface
{
    /** @var int[] */
    private $ids;

    /** @var int */
    private $total;

    /**
     * @param array $ids
     */
    public function __construct(array $ids = [])
    {
        $preparedIds = \array_filter($ids, 'is_numeric');
        $this->total = \count($preparedIds);

        if ($this->total !== \count($ids)) {
            throw new \InvalidArgumentException('Entity ID set must contain only integers.');
        }

        $this->ids = $preparedIds;
    }

    /**
     * @inheritdoc
     */
    public function getIds() : array
    {
        return $this->ids;
    }

    /**
     * @inheritdoc
     */
    public function getTotal() : int
    {
        return $this->total;
    }
}
