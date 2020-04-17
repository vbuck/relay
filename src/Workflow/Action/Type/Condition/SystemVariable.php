<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action\Type\Condition;

use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Api\Workflow\Action\TypeInterface;
use Relay\Data\SystemVariableRegistry;
use Relay\Workflow\Action\ParameterFactory;
use Relay\Workflow\Action\ResultFactory;

/**
 * Condition-based action for Relay system variables.
 */
class SystemVariable extends AbstractCondition implements TypeInterface
{
    /** @var ResultFactory */
    private $resultFactory;

    /** @var SystemVariableRegistry */
    private $systemVariableRegistry;

    /**
     * @param ParameterFactory $parameterFactory
     * @param ResultFactory $resultFactory
     * @param SystemVariableRegistry $systemVariableRegistry
     * @param string $defaultType
     */
    public function __construct(
        ParameterFactory $parameterFactory,
        ResultFactory $resultFactory,
        SystemVariableRegistry $systemVariableRegistry,
        string $defaultType = 'system_variable'
    ) {
        parent::__construct($parameterFactory, $defaultType);
        $this->resultFactory = $resultFactory;
        $this->systemVariableRegistry = $systemVariableRegistry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
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
        $expectedValue = (string) $this->systemVariableRegistry->get($parameters[static::PARAMETER_KEY]->getValue());
        $actualValue = (string) $parameters[static::PARAMETER_VALUE]->getValue();

        $result->setSuccessFlag(
            $this->compare($expectedValue, $actualValue, $parameters[static::PARAMETER_COMPARATOR]->getValue())
        );

        return $result;
    }
}
