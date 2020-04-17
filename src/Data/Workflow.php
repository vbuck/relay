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
 * Relay workflow data model. A workflow describes the actions which a handler must take to fulfill the request.
 */
class Workflow extends AbstractInjectableModel implements EntityInterface
{
    const DESCRIPTION = 'description';
    const NAME = 'name';
    const SEQUENCE = 'sequence';

    /** @var array */
    private $data = [
        self::ID => 0,
        self::NAME => '',
        self::DESCRIPTION => '',
        self::SEQUENCE => [],
    ];

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     * @param EntityMapperInterface $entityMapper
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        EntityMapperInterface $entityMapper,
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
            self::ID => $this->getEntityId(),
            self::NAME => $this->getName(),
            self::DESCRIPTION => $this->getDescription(),
            self::SEQUENCE => $this->serializer->serialize($this->getSequence()),
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
     * Get the workflow name.
     *
     * @return string
     */
    public function getName() : string
    {
        return (string) $this->data[static::NAME];
    }

    /**
     * Get the sequencing positions of the actions within the workflow.
     *
     * @return array A key-value map of [ SequenceNumber => ActionID, ... ]
     */
    public function getSequence() : array
    {
        return (array) $this->data[static::SEQUENCE];
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
     * Set the workflow name.
     *
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->data[static::NAME] = $name;
    }

    /**
     * Set the sequence of actions in the workflow.
     *
     * @param array $sequence
     */
    public function setSequence(array $sequence = []) : void
    {
        if (\count(\array_filter($sequence, 'is_numeric')) !== \count($sequence)) {
            throw new \InvalidArgumentException('Sequence must be a set of numeric action IDs.');
        };

        $this->data[static::SEQUENCE] = $sequence;
    }
}
