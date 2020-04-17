<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Relay\Api\TracerInterface;

/**
 * Factory for {@see TracerInterface} instances.
 */
class TracerFactory
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
     * Create a new tracer object.
     *
     * @param string $type
     * @return TracerInterface
     * @throws ConfigException
     */
    public function get(string $type = TracerInterface::class) : TracerInterface
    {
        return $this->app->getObject($type);
    }
}
