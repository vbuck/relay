<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api;

/**
 * Relay profile pool processor. Its job is to execute assigned workflows.
 *
 * Request > Relay Service > [ Profile Pool ] > Workflow Processor > Action Processor > Response
 */
interface ProfilePoolInterface
{
    /**
     * Process the given request by matching to profiles.
     *
     * @param RequestInterface $request
     * @return \Relay\Api\Workflow\Action\ResultInterface[]
     * @throws \Exception
     */
    public function process(RequestInterface $request) : array;
}
