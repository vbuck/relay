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
 * Relay profile data model. A profile contains routing instructions for Relay to use when handling requests.
 */
class Profile extends AbstractInjectableModel implements EntityInterface
{
    const DESCRIPTION = 'description';
    const NAME = 'name';
    const STATUS = 'status';
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    const WORKFLOW_ID = 'workflow_id';

    /** @var array */
    private $data = [
        self::ID => 0,
        self::NAME => '',
        self::DESCRIPTION => '',
        self::STATUS => '',
        self::WORKFLOW_ID => 0,
    ];

    /**
     * @inheritdoc
     */
    public function export() : array
    {
        return [
            static::ID => $this->getEntityId(),
            static::NAME => $this->getName(),
            static::DESCRIPTION => $this->getDescription(),
            static::STATUS => $this->getStatus(),
            static::WORKFLOW_ID => $this->getWorkflowId(),
        ];
    }

    /**
     * Get the profile description.
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
     * Get the profile name.
     *
     * @return string
     */
    public function getName() : string
    {
        return (string) $this->data[static::NAME];
    }

    /**
     * Get the profile status.
     *
     * @return int
     */
    public function getStatus() : int
    {
        return (int) $this->data[static::STATUS];
    }

    /**
     * Get the profile workflow ID.
     *
     * @return int
     */
    public function getWorkflowId() : int
    {
        return (int) $this->data[static::WORKFLOW_ID];
    }

    /**
     * Set the profile description.
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
     * Set the profile name.
     *
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->data[static::NAME] = $name;
    }

    /**
     * Set the profile status (active|inactive).
     *
     * @param bool $status
     */
    public function setStatus(bool $status) : void
    {
        $this->data[static::STATUS] = $status;
    }

    /**
     * Set the profile workflow ID.
     *
     * @param int $id
     */
    public function setWorkflowId(int $id) : void
    {
        $this->data[static::WORKFLOW_ID] = $id;
    }
}
