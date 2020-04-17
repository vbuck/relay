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
 * Process tracer utility. Designed to probe and record active processes by running as a service.
 */
interface TracerInterface extends \SplSubject
{
    /**
     * Detach all observers.
     *
     * @return void
     */
    public function detachAll() : void;

    /**
     * Register a traceable data point.
     *
     * Traces should be stored in a queue and distributed to attached observers.
     *
     * @param string $message A message to send as a trace descriptor.
     * @param array $tags Optional tags to classify the trace.
     * @param object|null $context Optional object to include for trace context.
     * @param array|null $backtrace Optional PHP backtrace to include.
     * @return void
     */
    public function trace(string $message, array $tags = [], $context = null, array $backtrace = null) : void;
}
