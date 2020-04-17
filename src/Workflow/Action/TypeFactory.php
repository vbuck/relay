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
use Relay\Api\Workflow\Action\TypeInterface;

/**
 * Factory for {@see TypeInterface} instances.
 */
class TypeFactory
{
    /** @var ApplicationManager */
    private $app;

    /** @var array */
    private $typeMap;

    /**
     * @param ApplicationManager|null $app
     * @codeCoverageIgnore
     */
    public function __construct(ApplicationManager $app = null)
    {
        $this->app = $app ?? ApplicationManager::getInstance();
    }

    /**
     * Create a new result object.
     *
     * @param string $typeCode
     * @return TypeInterface
     * @throws ConfigException
     */
    public function get(string $typeCode) : TypeInterface
    {
        $className = $this->getTypeClass($typeCode);

        if (empty($className)) {
            throw new \InvalidArgumentException(\sprintf('Action type "%s" is invalid.', $typeCode));
        }

        $instance = $this->app->getObject($className, []);

        if (!($instance instanceof TypeInterface)) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Requested type for "%s" must be an instance of %s.',
                    $typeCode,
                    TypeInterface::class
                )
            );
        }

        return $instance;
    }

    /**
     * Retrieve a type instance for the given code.
     *
     * @param string $code
     * @return string
     * @throws ConfigException
     */
    private function getTypeClass(string $code) : string
    {
        if (empty($this->typeMap)) {
            $this->typeMap = (array) $this->app->config(
                \sprintf('providers.%s.types', TypeInterface::class)
            );
        }

        return $this->typeMap[$code] ?? '';
    }
}
