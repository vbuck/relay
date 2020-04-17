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
 * Relay handler pool processor. Its job is to dispatch a request to one or more registered handlers.
 */
interface HandlerPoolInterface
{
    /**
     * Handle the given request and yield a processed result.
     *
     * Passes the request to every registered handler for attempt to process.
     *
     * @param RequestInterface $request
     * @return ResultInterface
     * @throws \Exception
     */
    public function handle(RequestInterface $request) : ResultInterface;

    /**
     * Get the registered handlers in whole or by types specified.
     *
     * @param array $types If specified, return a subset of handlers.
     * @return HandlerInterface[]
     */
    public function get(array $types = []) : array;
}
