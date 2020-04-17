<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action\Type\Merge;

use LowlyPHP\Service\Resource\EntityInterface;
use LowlyPHP\Service\Resource\SerializerInterface;
use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Api\Workflow\Action\TypeInterface;
use Relay\Route\RequestFactory;
use Relay\Workflow\Action\ParameterFactory;
use Relay\Workflow\Action\ResultFactory;

/**
 * Merge-based action.
 *
 * Combines user-defined parameters to augment the request.
 */
class Request implements TypeInterface
{
    const PARAMETER_USER_BODY_DATA = 'user_body_data';
    const PARAMETER_USER_HEADER_DATA = 'user_header_data';
    const PARAMETER_USER_REQUEST_DATA = 'user_request_data';

    /** @var ParameterFactory */
    private $parameterFactory;

    /** @var RequestFactory */
    private $requestFactory;

    /** @var ResultFactory */
    private $resultFactory;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param ParameterFactory $parameterFactory
     * @param RequestFactory $requestFactory
     * @param ResultFactory $resultFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ParameterFactory $parameterFactory,
        RequestFactory $requestFactory,
        ResultFactory $resultFactory,
        SerializerInterface $serializer
    ) {
        $this->parameterFactory = $parameterFactory;
        $this->requestFactory = $requestFactory;
        $this->resultFactory = $resultFactory;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function describe() : array
    {
        return [
            static::PARAMETER_USER_HEADER_DATA => $this->parameterFactory->create(
                static::PARAMETER_USER_HEADER_DATA, null, 'Request Headers', null
            ),
            static::PARAMETER_USER_REQUEST_DATA => $this->parameterFactory->create(
                static::PARAMETER_USER_REQUEST_DATA, null, 'Request Parameters', null
            ),
            static::PARAMETER_USER_BODY_DATA => $this->parameterFactory->create(
                static::PARAMETER_USER_BODY_DATA, null, 'Request Body', null
            ),
        ];
    }

    /**
     * {@inheritdoc}
     *
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

        // Always succeeds, whether or not anything was actually merged
        $result->setSuccessFlag(true);

        /** @var \Relay\Api\Workflow\Action\ParameterInterface[] $parameters */
        $parameters = \array_merge($this->describe(), $parameters);

        $this->mergeHeaders($request, (string) $parameters[static::PARAMETER_USER_HEADER_DATA]->getValue());
        $this->mergeRequestParameters($request, (string) $parameters[static::PARAMETER_USER_REQUEST_DATA]->getValue());
        $this->mergeBody($request, (string) $parameters[static::PARAMETER_USER_BODY_DATA]->getValue());

        return $result;
    }

    /**
     * Decode serialized data. Attempts to detect the format before decoding in case type is string.
     *
     * @param string $input
     * @return mixed
     */
    private function decode(string $input)
    {
        try {
            $decodedValue = $this->serializer->unserialize($input);
            if (!empty($input) && !empty($decodedValue)) {
                return $decodedValue;
            }

            return $input;
        } catch (\Exception $error) {
            return $input;
        }
    }

    /**
     * Merge the request body with the given user data.
     *
     * @param RequestInterface $request
     * @param string $userData
     */
    private function mergeBody(RequestInterface $request, string $userData = '') : void
    {
        if (empty($userData)) {
            return;
        }

        $body = $this->decode($request->getBody());

        if (\is_array($body)) {
            $result = $this->serializer->serialize(
                \array_merge($body, (array) $this->decode($userData))
            );
        } else {
            $result = $body . $userData;
        }

        $request->setBody($result);
    }

    /**
     * Merge the request headers with the given user data.
     *
     * @param RequestInterface $request
     * @param string $userData
     */
    private function mergeHeaders(RequestInterface $request, string $userData = '') : void
    {
        if (empty($userData)) {
            return;
        }

        $request->setHeaders(\array_merge($request->getHeaders(), (array) $this->decode($userData)));
    }

    /**
     * Merge the request parameters with the given user data.
     *
     * @param RequestInterface $request
     * @param string $userData
     */
    private function mergeRequestParameters(RequestInterface $request, string $userData = '') : void
    {
        if (empty($userData)) {
            return;
        }
        
        $request->setParameters(\array_merge($request->getParameters(), (array) $this->decode($userData)));
    }
}
