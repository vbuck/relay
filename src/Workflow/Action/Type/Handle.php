<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action\Type;

use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Api\HandlerPoolInterface;
use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Api\Workflow\Action\TypeInterface;
use Relay\Route\RequestFactory;
use Relay\Workflow\Action\ParameterFactory;
use Relay\Workflow\Action\ResultFactory;

/**
 * Handler-based action.
 *
 * Wraps handlers for execution and brings them into a workflow.
 */
class Handle implements TypeInterface
{
    const PARAMETER_TYPE = 'type';

    /** @var HandlerPoolInterface */
    private $handlerPool;

    /** @var ParameterFactory */
    private $parameterFactory;

    /** @var RequestFactory */
    private $requestFactory;

    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param HandlerPoolInterface $handlerPool
     * @param ParameterFactory $parameterFactory
     * @param RequestFactory $requestFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        HandlerPoolInterface $handlerPool,
        ParameterFactory $parameterFactory,
        RequestFactory $requestFactory,
        ResultFactory $resultFactory
    ) {
        $this->handlerPool = $handlerPool;
        $this->parameterFactory = $parameterFactory;
        $this->requestFactory = $requestFactory;
        $this->resultFactory = $resultFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function describe() : array
    {
        return [
            static::PARAMETER_TYPE => $this->parameterFactory->create(
                static::PARAMETER_TYPE, null, 'Handler Type', null
            ),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param EntityInterface $dataModel
     * @param RequestInterface $request
     * @param array $parameters
     * @param ResultInterface|null $result
     * @return ResultInterface
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function process(
        EntityInterface $dataModel,
        RequestInterface $request,
        array $parameters = [],
        ResultInterface $result = null
    ) : ResultInterface {
        if (!$result) {
            /** @var ResultInterface $result */
            $result = $this->resultFactory->create();
        }

        /** @var \Relay\Api\Workflow\Action\ParameterInterface[] $parameters */
        $parameters = \array_merge($this->describe(), $parameters);
        /** @var \Relay\Api\HandlerInterface $handler */
        $handler = \current($this->handlerPool->get([$parameters[static::PARAMETER_TYPE]->getValue()]));

        /** @var \Relay\Api\Handler\ResultInterface $handlerResult */
        $handlerResult = $handler->handle($request);

        $result->setSuccessFlag($handlerResult->isSuccessful());
        $result->setData(
            \array_merge(
                $result->getData(),
                [
                    'type' => $handlerResult->getType(),
                    'reference_id' => $handlerResult->getReferenceId(),
                    'timestamp' => $handlerResult->getTimestamp(),
                    'gateway_response' => $handlerResult->getGatewayResponse(),
                ]
            )
        );

        return $result;
    }
}
