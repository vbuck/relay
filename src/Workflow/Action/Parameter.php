<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action;

use Relay\Api\Workflow\Action\ParameterInterface;

/**
 * Implementation for {@see ParameterInterface}.
 */
class Parameter implements ParameterInterface
{
    /** @var string */
    private $id;

    /** @var string  */
    private $defaultValue;

    /** @var string */
    private $name;

    /** @var string */
    private $value;

    /**
     * @param string $id
     * @param string|null $value
     * @param string|null $name
     * @param string|null $defaultValue
     */
    public function __construct(
        string $id,
        string $value = null,
        string $name = null,
        string $defaultValue = null
    ) {
        $this->id = $id;
        $this->value = (string) $value;
        $this->name = $name ?: $id;
        $this->defaultValue = (string) $defaultValue;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultValue() : string
    {
        return (string) $this->defaultValue;
    }

    /**
     * @inheritdoc
     */
    public function getId() : string
    {
        return (string) $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getName() : string
    {
        return (string) $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getValue() : string
    {
        return (string) ($this->value ?: $this->getDefaultValue());
    }

    /**
     * @inheritdoc
     */
    public function setDefaultValue(string $value) : void
    {
        $this->defaultValue = $value;
    }

    /**
     * @inheritdoc
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function setValue(string $value) : void
    {
        $this->value = $value;
    }
}
