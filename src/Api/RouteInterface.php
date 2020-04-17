<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api;

use Relay\Route\Context;

/**
 * Standard route interface. Required for all registered endpoints.
 */
interface RouteInterface
{
    /**
     * Fulfillment the requested route and yield a response.
     *
     * @param RequestInterface $request
     * @param Context|null $context
     * @return ResponseInterface
     * @throws \Relay\Service\WebApiException
     */
    public function execute(RequestInterface $request, Context $context = null) : ResponseInterface;
}
