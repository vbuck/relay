<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Workflow\Action;

/**
 * Action result interface. Results are the outcome of the workflow which has passed through action processors.
 *
 * A result is designed to be passed through many actions. Its data can be changed through each transition.
 */
interface ResultInterface
{
    /**
     * Get the result data. This is a mixed result set of values resulting from an action.
     *
     * @return array
     */
    public function getData() : array;

    /**
     * Get the success flag. The flag represents the state of the last applied action.
     *
     * @return bool
     */
    public function getSuccessFlag() : bool;

    /**
     * Set the result set data associated with the action.
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data = []) : void;

    /**
     * Set the success flag.
     *
     * @param bool $state
     */
    public function setSuccessFlag(bool $state) : void;
}
