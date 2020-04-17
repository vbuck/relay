<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Handler;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Relay\Api\Handler\ResultInterface;

/**
 * Factory for {@see ResultInterface} instances.
 */
class ResultFactory
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
     * @param string $type
     * @param string $gatewayResponse
     * @param bool $state
     * @param string $referenceId
     * @param int|null $timestamp
     * @return ResultInterface
     * @throws ConfigException
     */
    public function create(
        string $type,
        string $gatewayResponse,
        bool $state,
        string $referenceId = '',
        int $timestamp = null
    ) : ResultInterface {
        return $this->app->createObject(
            ResultInterface::class,
            [
                'type' => $type,
                'gatewayResponse' => $gatewayResponse,
                'state' => $state,
                'referenceId' => $referenceId,
                'timestamp' => $timestamp,
            ]
        );
    }
}
