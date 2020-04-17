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
use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Api\Workflow\Action\TypeInterface;
use Relay\Data\SystemVariableRegistry;
use Relay\Route\RequestFactory;
use Relay\Workflow\Action\ParameterFactory;
use Relay\Workflow\Action\ResultFactory;

/**
 * System variable setter action.
 *
 * Sets system variables at runtime for handlers and other components to use. Values do not persist.
 */
class SetVariable implements TypeInterface
{
    const PARAMETER_NAME = 'name';
    const PARAMETER_VALUE = 'value';

    /** @var ParameterFactory */
    private $parameterFactory;

    /** @var RequestFactory */
    private $requestFactory;

    /** @var ResultFactory */
    private $resultFactory;

    /** @var SystemVariableRegistry */
    private $systemVariableRegistry;

    /**
     * @param ParameterFactory $parameterFactory
     * @param RequestFactory $requestFactory
     * @param ResultFactory $resultFactory
     * @param SystemVariableRegistry $systemVariableRegistry
     */
    public function __construct(
        ParameterFactory $parameterFactory,
        RequestFactory $requestFactory,
        ResultFactory $resultFactory,
        SystemVariableRegistry $systemVariableRegistry
    ) {
        $this->parameterFactory = $parameterFactory;
        $this->requestFactory = $requestFactory;
        $this->resultFactory = $resultFactory;
        $this->systemVariableRegistry = $systemVariableRegistry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function describe() : array
    {
        return [
            static::PARAMETER_NAME => $this->parameterFactory->create(
                static::PARAMETER_NAME, null, 'Variable Name', null
            ),
            static::PARAMETER_VALUE => $this->parameterFactory->create(
                static::PARAMETER_VALUE, null, 'Variable Value', null
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
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\EntityExistsException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     * @throws \LowlyPHP\Exception\StorageWriteException
     * @return ResultInterface
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

        // Always succeeds, whether or not anything was actually set
        $result->setSuccessFlag(true);

        /** @var \Relay\Api\Workflow\Action\ParameterInterface[] $parameters */
        $parameters = \array_merge($this->describe(), $parameters);

        if ($parameters[static::PARAMETER_NAME]->getValue()) {
            $this->systemVariableRegistry->set(
                (string) $parameters[static::PARAMETER_NAME]->getValue(),
                (string) $parameters[static::PARAMETER_VALUE]->getValue(),
                false
            );
        }

        return $result;
    }
}
