<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Utility;

use JmesPath\AstRuntime;
use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;

/**
 * Factory for {@see AstRuntime} instances.
 */
class JmesPathRuntimeFactory
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
     * @return AstRuntime
     * @throws ConfigException
     */
    public function get(string $type = AstRuntime::class) : AstRuntime
    {
        return $this->app->getObject($type);
    }
}
