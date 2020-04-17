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
 * Request interface for routes.
 */
interface RequestInterface
{
    /**
     * Get the raw request body contents.
     *
     * @return string
     */
    public function getBody() : string;

    /**
     * Get a request header by key.
     *
     * @param $key
     * @return string
     */
    public function getHeader($key) : string;

    /**
     * Get the input request headers as an array of key-value pairs.
     *
     * @return array
     */
    public function getHeaders() : array;

    /**
     * Get a request parameter by key.
     *
     * @param string $key
     * @return mixed
     */
    public function getParameter(string $key);

    /**
     * The the input request parameters as an array of key-value pairs.
     *
     * @return array
     */
    public function getParameters() : array;

    /**
     * Get the referring address.
     *
     * @return string
     */
    public function getReferrer() : string;

    /**
     * Set the raw request body contents.
     *
     * @param string $body
     */
    public function setBody(string $body);

    /**
     * Set a single request header.
     *
     * @param string $key
     * @param string $value
     */
    public function setHeader(string $key, string $value);

    /**
     * Set the input request headers from an array of key-value pairs.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers);

    /**
     * Set a request parameter by key.
     *
     * @param string $key
     * @param string $value
     */
    public function setParameter(string $key, string $value);

    /**
     * The the input request parameters as an array of key-value pairs.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * Set the referring address.
     *
     * @param string $referrer
     */
    public function setReferrer(string $referrer);
}
