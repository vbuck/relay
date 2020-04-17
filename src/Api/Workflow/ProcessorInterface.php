<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Workflow;

use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Data\Workflow;

/**
 * Workflow processor interface. Responsible for executing actions in a workflow sequence.
 *
 * Request > Relay Service > Profile Pool > [ Workflow Processor ] > Action Processor > Response
 */
interface ProcessorInterface
{
    /**
     * Process the given workflow's actions and return the final result.
     *
     * @param Workflow $workflow
     * @param RequestInterface $request
     * @return ResultInterface
     */
    public function process(Workflow $workflow, RequestInterface $request) : ResultInterface;
}
