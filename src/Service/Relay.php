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
use Relay\Api\ProfilePoolInterface;
use Relay\Route\Context;
use Relay\Api\ResponseInterface;
use Relay\Api\RouteInterface;
use Relay\Route\ResponseFactory;

/**
 * Web service entry point. Relay messages.
 *
 * Request > [ Relay Service ] > Profile Pool > Workflow Processor > Action Processor > Response
 */
class Relay implements RouteInterface
{
    /** @var array */
    private $messageMap = [
        200 => 'Request received.',
        207 => 'Partially succeeded.',
        400 => 'Request failed.',
    ];

    /** @var ProfilePoolInterface */
    private $profilePool;

    /** @var ResponseFactory */
    private $responseFactory;

    /**
     * @param ProfilePoolInterface $profilePool
     * @param ResponseFactory $responseFactory
     */
    public function __construct(ProfilePoolInterface $profilePool, ResponseFactory $responseFactory)
    {
        $this->profilePool = $profilePool;
        $this->responseFactory = $responseFactory;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function execute(RequestInterface $request, Context $context = null) : ResponseInterface
    {
        /** @var \Relay\Api\Workflow\Action\ResultInterface[] $results */
        $results = $this->profilePool->process($request);
        $statuses = [];
        $payload = [];

        /** @var \Relay\Api\Workflow\Action\ResultInterface $result */
        foreach ($results as $result) {
            $statuses[] = $result->getSuccessFlag();
            $payload[] = $result->getData();
        }

        $succeeded = \count(\array_filter($statuses));
        $code = (!empty($results) && $succeeded === \count($results)) ? 200 : ($succeeded > 0 ? 207 : 400);
        $message = $this->messageMap[$code];

        return $this->responseFactory->create($code, $message, $payload);
    }
}
