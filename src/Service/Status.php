<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Service;

use Relay\Api\RequestInterface;
use Relay\Api\ResponseInterface;
use Relay\Route\Context;
use Relay\Api\RouteInterface;
use Relay\Route\ResponseFactory;

/**
 * Service ping endpoint.
 */
class Status implements RouteInterface
{
    /** @var ResponseFactory */
    private $responseFactory;

    /**
     * @param ResponseFactory $responseFactory
     */
    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function execute(RequestInterface $request, Context $context = null) : ResponseInterface
    {
        return $this->responseFactory->create(200, 'Service OK');
    }
}
