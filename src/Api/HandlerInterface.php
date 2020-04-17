<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api;

use Relay\Api\Handler\ResultInterface;

/**
 * Relay handler interface. Handlers must respond to inbound requests for forwarding.
 */
interface HandlerInterface
{
    /**
     * Handle the given request and yield a standard result.
     *
     * @param RequestInterface $request
     * @return ResultInterface
     */
    public function handle(RequestInterface $request) : ResultInterface;
}
