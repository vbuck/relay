<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Handler;

/**
 * Relay handler result interface. Handlers must present a result for all processed requests.
 */
interface ResultInterface
{
    /**
     * Get the raw gateway response from the forwarded action.
     *
     * @return string
     */
    public function getGatewayResponse() : string;

    /**
     * Get the reference ID for the gateway transaction. Not required.
     *
     * @return string
     */
    public function getReferenceId() : string;

    /**
     * Get the RFC-2822 timestamp of the gateway response receipt.
     *
     * @return string
     */
    public function getTimestamp() : string;

    /**
     * Get the handler type for the current result.
     *
     * @return string
     */
    public function getType() : string;

    /**
     * Determine whether the gateway response is considered successful to its handler.
     *
     * @return bool
     */
    public function isSuccessful() : bool;
}
