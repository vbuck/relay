<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action\Type\Transform;

use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Api\Workflow\Action\TypeInterface;
use Relay\Workflow\Action\ParameterFactory;
use Relay\Workflow\Action\ResultFactory;

/**
 * Request transform action.
 *
 * Transforms request data based on defined inputs.
 */
class Request implements TypeInterface
{
    const PARAMETER_NAME = 'name';
    const PARAMETER_PATTERN = 'pattern';
    const PARAMETER_REPLACEMENT = 'replacement';
    const PARAMETER_TYPE = 'type';
    const TYPE_BODY = 'body';
    const TYPE_HEADER = 'header';
    const TYPE_PARAM = 'param';

    /** @var ParameterFactory */
    private $parameterFactory;
    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param ParameterFactory $parameterFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(ParameterFactory $parameterFactory, ResultFactory $resultFactory)
    {
        $this->parameterFactory = $parameterFactory;
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
                static::PARAMETER_TYPE, null, 'Parameter Type', static::TYPE_PARAM
            ),
            static::PARAMETER_NAME => $this->parameterFactory->create(
                static::PARAMETER_NAME, null, 'Parameter Name', null
            ),
            static::PARAMETER_PATTERN => $this->parameterFactory->create(
                static::PARAMETER_PATTERN, null, 'Parameter Value Pattern', null
            ),
            static::PARAMETER_REPLACEMENT => $this->parameterFactory->create(
                static::PARAMETER_REPLACEMENT, null, 'Parameter Value Replacement', null
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

        // Always succeeds, whether or not anything was actually transformed
        $result->setSuccessFlag(true);

        /** @var \Relay\Api\Workflow\Action\ParameterInterface[] $parameters */
        $parameters = \array_merge($this->describe(), $parameters);
        $pattern = $parameters[static::PARAMETER_PATTERN]->getValue();
        $replacement = $parameters[static::PARAMETER_REPLACEMENT]->getValue();

        switch ($parameters[static::PARAMETER_TYPE]->getValue()) {
            case static::TYPE_BODY:
                $request->setBody($this->transform((string) $request->getBody(), $pattern, $replacement));
                break;
            case static::TYPE_HEADER:
                $request->setHeader(
                    $parameters[static::PARAMETER_NAME]->getValue(),
                    $this->transform(
                        (string) $request->getHeader($parameters[static::PARAMETER_NAME]->getValue()),
                        $pattern,
                        $replacement
                    )
                );
                break;
            case static::TYPE_PARAM:
            default:
                $request->setParameter(
                    $parameters[static::PARAMETER_NAME]->getValue(),
                    $this->transform(
                        (string) $request->getParameter($parameters[static::PARAMETER_NAME]->getValue()),
                        $pattern,
                        $replacement
                    )
                );
                break;
        }

        return $result;
    }

    /**
     * Apply a transformation to the given input.
     *
     * @param string $input
     * @param string $pattern
     * @param string $replacement
     * @return string
     */
    private function transform(string $input = '', string $pattern = '', string $replacement = '') : string
    {
        return \preg_replace(
            '/' . \str_replace('/', '\\/', $pattern) . '/',
            $replacement,
            $input
        );
    }
}
