<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action;

use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Api\Workflow\Action\ProcessorInterface;
use Relay\Data\Action;

/**
 * Implementation for {@see ProcessorInterface}.
 */
class Processor implements ProcessorInterface
{
    /** @var ResultFactory */
    private $resultFactory;

    /** @var TypeFactory */
    private $typeFactory;

    /**
     * @param ResultFactory $resultFactory
     * @param TypeFactory $typeFactory
     */
    public function __construct(ResultFactory $resultFactory, TypeFactory $typeFactory)
    {
        $this->resultFactory = $resultFactory;
        $this->typeFactory = $typeFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function process(
        Action $action,
        RequestInterface $request,
        ResultInterface $result = null
    ) : ResultInterface {
        if (!$result) {
            /** @var ResultInterface $result */
            $result = $this->resultFactory->create();
        }

        /** @var \Relay\Api\Workflow\Action\TypeInterface $typeProcessor */
        $typeProcessor = $this->typeFactory->get($action->getType());
        $parameters = $typeProcessor->describe();

        // Parameters of the action are initialized before dispatching to the type processor
        foreach ($action->getParameters() as $key => $value) {
            if (!empty($parameters[$key])) {
                $parameters[$key]->setValue($value);
            }
        }

        return $typeProcessor->process($action, $request, $parameters, $result);
    }
}
