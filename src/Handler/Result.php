<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Handler;

use Relay\Api\Handler\ResultInterface;

/**
 * Handler result implementation for {@see ResultInterface}.
 */
class Result implements ResultInterface
{
    /** @var string */
    private $gatewayResponse;

    /** @var string */
    private $referenceId;

    /** @var bool */
    private $state;

    /** @var int */
    private $timestamp;

    /** @var string */
    private $type;

    /**
     * @param string $type
     * @param string $gatewayResponse
     * @param bool $state
     * @param string $referenceId
     * @param int|null $timestamp
     */
    public function __construct(
        string $type,
        string $gatewayResponse,
        bool $state,
        string $referenceId = '',
        int $timestamp = null
    ) {
        $this->type = $type;
        $this->gatewayResponse = $gatewayResponse;
        $this->state = $state;
        $this->referenceId = $referenceId;
        $this->timestamp = $timestamp ?: \time();
    }

    /**
     * @inheritdoc
     */
    public function getGatewayResponse() : string
    {
        return (string) $this->gatewayResponse;
    }

    /**
     * @inheritdoc
     */
    public function getReferenceId() : string
    {
        return (string) $this->referenceId;
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp() : string
    {
        return \date('r', $this->timestamp);
    }

    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function isSuccessful() : bool
    {
        return (bool) $this->state;
    }
}
