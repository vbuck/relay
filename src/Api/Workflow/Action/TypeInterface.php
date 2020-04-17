<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Workflow\Action;

use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Api\RequestInterface;

/**
 * Workflow action type interface. A typed action is also a processor, handling the business logic of the DTO.
 */
interface TypeInterface
{
    /**
     * Describe the input parameters for this type.
     *
     * @return ParameterInterface[] An array of parameters indexed by parameter ID.
     */
    public function describe() : array;

    /**
     * Process a request using the provided data model. Returns a standard result whether or not it has been provided.
     *
     * Parameters are detached from the data model, but when the model supports ParameterInterface[], they should be
     * merged with the input parameters. This enables a processor to be configured by its caller.
     *
     * @param EntityInterface $dataModel The action data model
     * @param RequestInterface $request The original client request
     * @param ParameterInterface[] $parameters Parameters associated with the data model
     * @param ResultInterface|null $result An existing result in the chain of applied actions
     * @return ResultInterface
     */
    public function process(
        EntityInterface $dataModel,
        RequestInterface $request,
        array $parameters = [],
        ResultInterface $result = null
    ) : ResultInterface;
}
