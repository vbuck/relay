<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Route;

use Relay\Api\RequestInterface;

/**
 * Request implementation for {@see RequestInterface}.
 */
class Request implements RequestInterface
{
    /** @var array */
    private $headers;

    /** @var array */
    private $parameters;

    /** @var string */
    private $rawBody;

    /** @var string */
    private $referrer;

    /**
     * @param array $headers
     * @param array $parameters
     * @param string $referrer
     * @param string $rawBody
     */
    public function __construct(
        array $headers = [],
        array $parameters = [],
        string $referrer = '',
        string $rawBody = ''
    ) {
        $this->headers = $headers;
        $this->parameters = $parameters;
        $this->referrer = $referrer;
        $this->rawBody = $rawBody;
    }

    /**
     * @inheritdoc
     */
    public function getBody() : string
    {
        return (string) $this->rawBody;
    }

    /**
     * @inheritdoc
     */
    public function getHeader($key) : string
    {
        return $this->headers[\strtolower($key)] ?? '';
    }

    /**
     * @inheritdoc
     */
    public function getHeaders() : array
    {
        return (array) $this->headers;
    }

    /**
     * @inheritdoc
     */
    public function getParameter(string $key)
    {
        return $this->parameters[$key] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function getReferrer() : string
    {
        return (string) $this->referrer;
    }

    /**
     * @inheritdoc
     */
    public function setBody(string $body)
    {
        $this->rawBody = $body;
    }

    /**
     * @inheritdoc
     */
    public function setHeader(string $key, string $value)
    {
        $this->headers[\strtolower($key)] = $value;
    }

    /**
     * @inheritdoc
     */
    public function setHeaders(array $headers)
    {
        $this->headers = [];

        foreach ($headers as $key => $value) {
            $this->headers[\strtolower($key)] = \strval($value);
        }
    }

    /**
     * @inheritdoc
     */
    public function setParameter(string $key, string $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @inheritdoc
     */
    public function setReferrer(string $referrer)
    {
        $this->referrer = $referrer;
    }
}
