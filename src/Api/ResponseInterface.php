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
 * Response interface for requests.
 */
interface ResponseInterface
{
    /**
     * Get the HTTP response code.
     *
     * @return int
     */
    public function getCode() : int;

    /**
     * Get a message associated with the response.
     *
     * @return string
     */
    public function getMessage() : string;

    /**
     * Get the result set for the request.
     *
     * @return array
     */
    public function getResult() : array;

    /**
     * Serialize the response data into a portable string.
     *
     * @return string
     */
    public function serialize() : string;
}
