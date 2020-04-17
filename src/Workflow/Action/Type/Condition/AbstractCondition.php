<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action\Type\Condition;

use Relay\Workflow\Action\ParameterFactory;

/**
 * Condition-based action processor.
 *
 * A condition adds a decision to a workflow based on values from a source. All conditions must declare a type, a
 * target property key, comparison operator, and comparison value. An example might be:
 *
 *     "Succeed when request parameter 'threshold' is greater than 5"
 *     type: request; key: threshold; comparator: >; value: 5
 */
abstract class AbstractCondition
{
    const COMPARATOR_CONTAINS = 'contains';
    const COMPARATOR_NCONTAINS = 'not_contains';
    const COMPARATOR_EQ = '=';
    const COMPARATOR_GT = '>';
    const COMPARATOR_GTE = '>=';
    const COMPARATOR_LT = '<';
    const COMPARATOR_LTE = '<=';
    const COMPARATOR_NEQ = '!=';

    const PARAMETER_COMPARATOR = 'comparator';
    const PARAMETER_KEY = 'key';
    const PARAMETER_TYPE = 'type';
    const PARAMETER_VALUE = 'value';

    /** @var string */
    protected $defaultType;

    /** @var ParameterFactory */
    protected $parameterFactory;

    /**
     * @param ParameterFactory $parameterFactory
     * @param string|null $defaultType
     */
    public function __construct(ParameterFactory $parameterFactory, string $defaultType = null)
    {
        $this->parameterFactory = $parameterFactory;
        $this->defaultType = $defaultType;
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
                static::PARAMETER_TYPE, null, 'Type', $this->defaultType
            ),
            static::PARAMETER_KEY => $this->parameterFactory->create(
                static::PARAMETER_KEY, null, 'Key', null
            ),
            static::PARAMETER_COMPARATOR => $this->parameterFactory->create(
                static::PARAMETER_COMPARATOR, null, 'Comparator', null
            ),
            static::PARAMETER_VALUE => $this->parameterFactory->create(
                static::PARAMETER_VALUE, null, 'Value', null
            ),
        ];
    }

    /**
     * Compare two values for equality.
     *
     * Input must be given as strings. Comparisons are performed based on loose equality and are case-insensitive.
     *
     * @param string $expected
     * @param string $actual
     * @param string $comparator
     * @return bool
     */
    public function compare(string $expected, string $actual, string $comparator = self::COMPARATOR_EQ) : bool
    {
        switch ($comparator) {
            case static::COMPARATOR_CONTAINS:
                return \stristr($expected, $actual) !== false;
            case static::COMPARATOR_NCONTAINS:
                return \stristr($expected, $actual) === false;
            case static::COMPARATOR_GT:
                return $expected > $actual;
            case static::COMPARATOR_GTE:
                return $expected >= $actual;
            case static::COMPARATOR_LT:
                return $expected < $actual;
            case static::COMPARATOR_LTE:
                return $expected <= $actual;
            case static::COMPARATOR_NEQ:
                return $expected != $actual;
            case static::COMPARATOR_EQ:
            default:
                return $expected == $actual;
        }
    }
}
