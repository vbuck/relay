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

/**
 * Relay system variable data model. System variables are data used by components during relay.
 */
class SystemVariable extends AbstractInjectableModel implements EntityInterface
{
    const NAME = 'name';
    const VALUE = 'value';

    /** @var array */
    private $data = [
        self::ID => 0,
        self::NAME => '',
        self::VALUE => '',
    ];

    /**
     * @inheritdoc
     */
    public function export() : array
    {
        return [
            static::ID => $this->getEntityId(),
            static::NAME => $this->getName(),
            static::VALUE => $this->getValue(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getEntityId() : int
    {
        return (int) $this->data[static::ID];
    }

    /**
     * Get the variable name.
     *
     * @return string
     */
    public function getName() : string
    {
        return (string) $this->data[static::NAME];
    }

    /**
     * Get the variable value.
     *
     * @return string
     */
    public function getValue() : string
    {
        return (string) $this->data[static::VALUE];
    }

    /**
     * @inheritdoc
     */
    public function setEntityId(int $id) : void
    {
        $this->data[static::ID] = $id;
    }

    /**
     * Set the variable name.
     *
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->data[static::NAME] = $name;
    }

    /**
     * Set the variable value.
     *
     * @param string $value
     */
    public function setValue(string $value) : void
    {
        $this->data[static::VALUE] = $value;
    }
}
