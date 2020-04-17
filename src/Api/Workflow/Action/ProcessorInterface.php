<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Workflow\Action;

use Relay\Api\RequestInterface;
use Relay\Data\Action;

/**
 * Action processor interface.
 *
 * Request > Relay Service > Profile Pool > Workflow Processor > [ Action Processor ] > Response
 */
interface ProcessorInterface
{
    /**
     * Process the given action and return its result.
     *
     * @param Action $action
     * @param RequestInterface $request
     * @param ResultInterface|null $result
     * @return ResultInterface
     */
    public function process(
        Action $action,
        RequestInterface $request,
        ResultInterface $result = null
    ) : ResultInterface;
}
