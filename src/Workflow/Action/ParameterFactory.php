<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Relay\Api\Workflow\Action\ParameterInterface;

/**
 * Factory for {@see ParameterInterface} instances.
 */
class ParameterFactory
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
     * @param string $id
     * @param string|null $value
     * @param string|null $name
     * @param string|null $defaultValue
     * @return ParameterInterface
     * @throws ConfigException
     */
    public function create(
        string $id,
        string $value = null,
        string $name = null,
        string $defaultValue = null
    ) : ParameterInterface {
        return $this->app->createObject(
            ParameterInterface::class,
            [
                'id' => $id,
                'value' => $value,
                'name' => $name,
                'defaultValue' => $defaultValue,
            ]
        );
    }
}
