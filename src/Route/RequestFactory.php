<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Route;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Relay\Api\RequestInterface;

/**
 * Factory for {@see RequestInterface} instances.
 */
class RequestFactory
{
    /** @var ApplicationManager */
    private $app;

    /**
     * @param ApplicationManager|null $app
     * @codeCoverageIgnore
     */
    public function __construct(ApplicationManager $app = null)
    {
        $this->app = $app ?? ApplicationManager::getInstance();
    }

    /**
     * Create a new context object.
     *
     * @param array $headers
     * @param array $parameters
     * @param string $referrer
     * @param string $rawBody
     * @return RequestInterface
     * @throws ConfigException
     */
    public function create(
        array $headers = [],
        array $parameters = [],
        string $referrer = '',
        string $rawBody = ''
    ) : RequestInterface
    {
        return $this->app->createObject(
            RequestInterface::class,
            [
                'headers' => $headers,
                'parameters' => $parameters,
                'referrer' => $referrer,
                'rawBody' => $rawBody,
            ]
        );
    }
}
