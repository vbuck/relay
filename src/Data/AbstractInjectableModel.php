<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Data;

use LowlyPHP\Service\Resource\EntityInterface;
use LowlyPHP\Service\Resource\EntityMapperInterface;

/**
 * A DTO abstraction which supports type-validated value injection.
 */
abstract class AbstractInjectableModel
{
    /**
     * @param EntityMapperInterface $entityMapper
     * @param array $data
     */
    public function __construct(EntityMapperInterface $entityMapper, array $data = [])
    {
        // Gracefully abort injection mapping if the extending class would not actually support it
        if (!isset($this->data) || !($this instanceof EntityInterface)) {
            return;
        }

        /** @var EntityInterface $instance */
        $instance = $this;

        foreach ($data as $key => $value) {
            if (!isset($this->data[$key])) {
                throw new \InvalidArgumentException(\sprintf('Property "%s" is invalid.', $key));
            }
        }

        $entityMapper->map($data, $instance);
    }
}
