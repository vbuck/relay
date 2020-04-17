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
use Relay\Api\Tracer\ObserverInterface;

/**
 * Factory for {@see ObserverInterface} instances.
 */
class ObserverFactory
{
    /** @var ApplicationManager */
    private $app;

    /** @var array */
    private $typeMap;

    /**
     * @param ApplicationManager|null $app
     * @throws ConfigException
     * @codeCoverageIgnore
     */
    public function __construct(ApplicationManager $app = null)
    {
        $this->app = $app ?? ApplicationManager::getInstance();
        $this->typeMap = (array) $app->config(
            \sprintf('providers.%s.types', ObserverInterface::class)
        );
    }

    /**
     * Create a new observer object.
     *
     * @param string $type
     * @param array $data
     * @return ObserverInterface
     * @throws ConfigException
     */
    public function create(string $type, array $data = []) : ObserverInterface
    {
        if (!\class_exists($type) && empty($this->typeMap[$type])) {
            throw new \InvalidArgumentException(
                \sprintf('Type "%s" is not a registered observer.', $type)
            );
        }

        /** @var ObserverInterface $instance */
        $instance = $this->app->createObject($type, $data);

        if (!($instance instanceof ObserverInterface)) {
            throw new \InvalidArgumentException(
                \sprintf('Type "%s" must be an instance of %s', $type, ObserverInterface::class)
            );
        }

        return $instance;
    }
}
