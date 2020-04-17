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
use Relay\Workflow\Action\ParameterFactory;
use Relay\Workflow\Action\ResultFactory;

/**
 * Merge-based action.
 *
 * Combines user-defined parameters to augment the result.
 */
class Result implements TypeInterface
{
    const PARAMETER_USER_DATA = 'user_data';

    /** @var ParameterFactory */
    private $parameterFactory;

    /** @var ResultFactory */
    private $resultFactory;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param ParameterFactory $parameterFactory
     * @param ResultFactory $resultFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ParameterFactory $parameterFactory,
        ResultFactory $resultFactory,
        SerializerInterface $serializer
    ) {
        $this->parameterFactory = $parameterFactory;
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
            static::PARAMETER_USER_DATA => $this->parameterFactory->create(
                static::PARAMETER_USER_DATA, null, 'Response Body', null
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

        $this->merge($result, (string) $parameters[static::PARAMETER_USER_DATA]->getValue());

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
     * Merge the result with the given user data.
     *
     * @param ResultInterface $result
     * @param string $userData
     */
    private function merge(ResultInterface $result, string $userData = '') : void
    {
        if (empty($userData)) {
            return;
        }

        $result->setData(
            \array_merge($result->getData(), (array) $this->decode($userData))
        );
    }
}
