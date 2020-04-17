<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   PSA Assessment, Pet Store Project
 * @license   MIT
 */

namespace Relay\Data\SystemVariable;

use LowlyPHP\Provider\Resource\Storage\Schema as BaseSchema;
use LowlyPHP\Provider\Resource\Storage\Schema\ColumnFactory;
use LowlyPHP\Service\Resource\Storage\SchemaStorageInterface;
use Relay\Data\SystemVariable;

/**
 * System variable schema definition.
 *
 * Designates the variable name as a unique identifier and imposes length restrictions.
 */
class Schema extends BaseSchema
{
    const NAME_LENGTH = '75';

    /**
     * @param string $name
     * @param string $source
     * @param \LowlyPHP\Service\Resource\Storage\Schema\ColumnInterface[] $columns
     * @param ColumnFactory $columnFactory
     * @throws \InvalidArgumentException
     * @throws \LowlyPHP\Exception\ConfigException
     * @codeCoverageIgnore
     */
    public function __construct(
        string $name,
        string $source,
        array $columns,
        ColumnFactory $columnFactory
    ) {
        foreach ($columns as $index => $column) {
            /** @var array $metadata */
            $metadata = $column->getMetadata();

            if ($column->getName() === SystemVariable::NAME) {
                $metadata[SchemaStorageInterface::META_KEY_INDEXES] = [
                    ['unique' => true]
                ];

                $columns[$index] = $columnFactory->create(
                    $column->getName(),
                    static::NAME_LENGTH,
                    $column->getType(),
                    $metadata
                );
            }
        }

        parent::__construct($name, $source, $columns);
    }
}
