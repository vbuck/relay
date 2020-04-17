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
use LowlyPHP\Service\Resource\SerializerInterface;

/**
 * Relay action data model. Actions are used to modify and route requests to handlers.
 */
class Action extends AbstractInjectableModel implements EntityInterface
{
    const DESCRIPTION = 'description';
    const TYPE = 'type';
    const PARAMETERS = 'parameters';

    /** @var array */
    private $data = [
        self::ID => 0,
        self::DESCRIPTION => '',
        self::TYPE => '',
        self::PARAMETERS => [],
    ];

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param EntityMapperInterface $entityMapper
     * @param SerializerInterface $serializer
     * @param array $data
     */
    public function __construct(
        EntityMapperInterface $entityMapper,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($entityMapper, $data);

        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function export() : array
    {
        return [
            static::ID => $this->getEntityId(),
            static::DESCRIPTION => $this->getDescription(),
            static::TYPE => $this->getType(),
            static::PARAMETERS => $this->serializer->serialize($this->getParameters()),
        ];
    }

    /**
     * Get the workflow description.
     *
     * @return string
     */
    public function getDescription() : string
    {
        return (string) $this->data[static::DESCRIPTION];
    }

    /**
     * @inheritdoc
     */
    public function getEntityId() : int
    {
        return (int) $this->data[static::ID];
    }

    /**
     * Get the input parameters of the action.
     *
     * @return array
     */
    public function getParameters() : array
    {
        return (array) $this->data[static::PARAMETERS];
    }

    /**
     * Get the action type code.
     *
     * @return string
     */
    public function getType() : string
    {
        return (string) $this->data[static::TYPE];
    }

    /**
     * Set the workflow description.
     *
     * @param string $description
     */
    public function setDescription(string $description) : void
    {
        $this->data[static::DESCRIPTION] = $description;
    }

    /**
     * @inheritdoc
     */
    public function setEntityId(int $id) : void
    {
        $this->data[static::ID] = $id;
    }

    /**
     * Set the parameters of the action.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters = []) : void
    {
        $this->data[static::PARAMETERS] = $parameters;
    }

    /**
     * Set the action type code.
     *
     * @param string $type
     */
    public function setType(string $type) : void
    {
        $this->data[static::TYPE] = $type;
    }
}
