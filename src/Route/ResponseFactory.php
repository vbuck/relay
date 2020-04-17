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
use Relay\Api\ResponseInterface;

/**
 * Factory for {@see ResponseInterface} instances.
 */
class ResponseFactory
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
     * @param int $code
     * @param string $message
     * @param array $result
     * @return ResponseInterface
     * @throws ConfigException
     */
    public function create(
        int $code,
        string $message,
        array $result = []
    ) : ResponseInterface
    {
        return $this->app->createObject(
            ResponseInterface::class,
            [
                'code' => $code,
                'message' => $message,
                'result' => $result,
            ]
        );
    }
}
