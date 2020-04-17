<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Data;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;

/**
 * Factory for {@see SystemVariable} instances.
 */
class SystemVariableFactory
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
     * Create a new system variable object.
     *
     * @param array $data
     * @return SystemVariable
     * @throws ConfigException
     */
    public function create(array $data = []) : SystemVariable
    {
        return $this->app->createObject(SystemVariable::class, ['data' => $data]);
    }
}
