<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Tracer;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Relay\Api\Tracer\DataInterface;

/**
 * Factory for {@see DataInterface} instances.
 */
class DataFactory
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
     * Create a new tracer data point object.
     *
     * @param string $message
     * @param object|null $context
     * @param array $backtrace
     * @return DataInterface
     * @throws ConfigException
     */
    public function create(string $message, $context = null, array $backtrace = []) : DataInterface
    {
        return $this->app->createObject(
        DataInterface::class,
            [
                'message' => $message,
                'context' => $context,
                'backtrace' => $backtrace,
            ]
        );
    }
}
