<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Tracer;

/**
 * Tracer process data point. Wraps traceable data with common accessor methods.
 */
interface DataInterface extends \SplSubject
{
    /**
     * Get the PHP backtrace associated with the data point.
     *
     * @return array
     */
    public function getBacktrace() : array;

    /**
     * Get the associated context object of the data point.
     *
     * @return mixed
     */
    public function getContext();

    /**
     * Get the descriptor of the data point.
     *
     * @return string
     */
    public function getMessage() : string;
}
