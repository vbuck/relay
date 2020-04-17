<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Tracer;

use LowlyPHP\Service\Resource\EntityInterface;

/**
 * Tracer process observer. Attached as a listener to the application for observing behavior.
 */
interface ObserverInterface extends \SplObserver, \Serializable, EntityInterface
{
    const METADATA = 'metadata';
    const ORIGIN_ID = 'origin_id';
    const RESOURCE_NAME = 'resource_name';
    const TAGS = 'tags';
    const TYPE = 'type';

    /**
     * Get metadata about the observer instance. Provides type-specific key-value pairs of data.
     *
     * @return array
     */
    public function getMetadata() : array;

    /**
     * Get the origination ID for the observer instance.
     *
     * Origin IDs are used in persistence of the observer across requests and represent a hash of parameters based on
     * the origination point and registration of the observer. An example of this would be to incorporate the PID,
     * origin hostname, and any other associated classification or taxonomy.
     *
     * @return string
     */
    public function getOriginId() : string;

    /**
     * Get the resource name of the observer. A generic term for a file path, URI, or stream.
     *
     * @return string
     */
    public function getResourceName() : string;

    /**
     * Get the observable tags. Tags are used to subscribe to specific types of trace messages.
     *
     * @return array
     */
    public function getTags() : array;

    /**
     * Get the observer declared type code. Types provide a public-facing label for the instance.
     *
     * @return string
     */
    public function getType() : string;
}
