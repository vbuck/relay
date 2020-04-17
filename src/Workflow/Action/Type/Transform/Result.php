<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action\Type\Transform;

use JmesPath\Parser;
use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Api\Workflow\Action\TypeInterface;
use Relay\Utility\JmesPathRuntimeFactory;
use Relay\Workflow\Action\ParameterFactory;
use Relay\Workflow\Action\ResultFactory;

/**
 * Result transform action.
 *
 * Transforms result data based on defined inputs.
 */
class Result implements TypeInterface
{
    const PARAMETER_NAME = 'name';
    const PARAMETER_PATTERN = 'pattern';
    const PARAMETER_REPLACEMENT = 'replacement';

    /** @var JmesPathRuntimeFactory */
    private $jmesPathRuntimeFactory;

    /** @var ParameterFactory */
    private $parameterFactory;

    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param ParameterFactory $parameterFactory
     * @param ResultFactory $resultFactory
     * @param JmesPathRuntimeFactory $jmesPathRuntimeFactory
     */
    public function __construct(
        ParameterFactory $parameterFactory,
        ResultFactory $resultFactory,
        JmesPathRuntimeFactory $jmesPathRuntimeFactory
    ) {
        $this->parameterFactory = $parameterFactory;
        $this->resultFactory = $resultFactory;
        $this->jmesPathRuntimeFactory = $jmesPathRuntimeFactory;
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
                static::PARAMETER_NAME, null, 'Result Value Path', null
            ),
            static::PARAMETER_PATTERN => $this->parameterFactory->create(
                static::PARAMETER_PATTERN, null, 'Optional Value Pattern', null
            ),
            static::PARAMETER_REPLACEMENT => $this->parameterFactory->create(
                static::PARAMETER_REPLACEMENT, null, 'Value Replacement', null
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

        $this->transform(
            $result,
            $parameters[static::PARAMETER_NAME]->getValue(),
            $pattern,
            $replacement
        );

        return $result;
    }

    /**
     * Write the value at the given path to the result.
     *
     * Very primitive support of expressions, cannot interpret all lexical symbols.
     *
     * @param ResultInterface $result
     * @param string $path
     * @param mixed $value
     * @param array $context An optional data context to search
     * @param array|null $ast An optional AST result set
     */
    private function commit(
        ResultInterface $result,
        string $path,
        $value,
        array &$context = [],
        array $ast = null
    ) : void {
        !$context && $context = $result->getData();
        !$ast && $ast = $this->evaluate($path);

        /**
         * @var int $index
         * @var array $metadata
         */
        foreach ($ast['children'] as $index => $metadata) {
            if (!isset($metadata['type'])) {
                continue;
            }

            $key = null;

            switch ($metadata['type']) {
                case 'field':
                case 'index':
                    $key = $metadata['value'];
                    break;
            }

            if (!$key) {
                continue;
            } elseif (\is_array($context[$key])) {
                $this->commit(
                    $result,
                    $path,
                    $value,
                    $context[$key],
                    ['children' => \array_slice($ast['children'], $index + 1)]
                );
            } else {
                $context[$key] = $value;
            }
        }

        $result->setData($context);
    }

    /**
     * Evaluate the given path and parse its structure.
     *
     * @param string $path
     * @return array
     */
    private function evaluate(string $path) : array
    {
        /** @var Parser $parser */
        $parser = new Parser();
        /** @var array $astResult */
        $result = $parser->parse($path);

        if (empty($result['children'])) {
            // Normalize structure to treat all AST results as child nodes
            $result['children'] = [$result];
        }

        return $result;
    }

    /**
     * Apply a transformation to the given input.
     *
     * @param ResultInterface $result
     * @param string $path The result data path in JSON dot-notation.
     * @param string $pattern An optional pattern by which to match and replace the value.
     * @param string $replacement The value replacement.
     * @return void
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \Exception
     */
    private function transform(
        ResultInterface $result,
        string $path = '',
        string $pattern = '',
        string $replacement = ''
    ) : void {
        /** @var \JmesPath\AstRuntime $runtime */
        $runtime = $this->jmesPathRuntimeFactory->get();
        /** @var array $input */
        $input = $result->getData();
        /** @var mixed $value */
        $value = $runtime($path, $input);

        // Do not apply transforms when the path expression did not yield a match
        if ($value === null) {
            return;
        } elseif (!\is_scalar($value)) {
            // @todo Complex transformations are not supported at this time
            throw new \Exception(
                \sprintf('Non-scalar result transformation on path "%s" is not supported.', $path)
            );
        }

        $type = gettype($value);

        if (!empty($pattern)) {
            $value = \preg_replace(
                '/' . \str_replace('/', '\\/', $pattern) . '/',
                $replacement,
                (string) $value
            );
        } else {
            $value = $replacement;
        }

        // Attempt to preserve original type after a transformation
        \settype($value, $type);

        $this->commit($result, $path, $value);
    }
}
