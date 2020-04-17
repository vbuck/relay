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

/**
 * Factory for {@see Context} instances.
 */
class ContextFactory
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
     * @param string $host
     * @param string $area
     * @param bool $secure
     * @return Context
     * @throws ConfigException
     */
    public function create(
        string $host,
        string $area = 'default',
        bool $secure = false
    ) : Context
    {
        return $this->app->createObject(
            Context::class,
            [
                'host' => $host,
                'area' => $area,
                'secure' => $secure,
            ]
        );
    }
}
