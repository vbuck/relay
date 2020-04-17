<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console;

use LowlyPHP\ApplicationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class CommandLoader implements CommandLoaderInterface
{
    const COMMAND_CONFIG = 'console.commands';

    /** @var ApplicationManager */
    private $app;

    /** @var Command[] */
    private $commands;

    /**
     * @param ApplicationManager|null $app
     */
    public function __construct(ApplicationManager $app = null)
    {
        $this->app = $app ?? ApplicationManager::getInstance();
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function get($name)
    {
        $this->load();

        if (!isset($this->commands[$name])) {
            throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
        }

        return $this->app->createObject($this->commands[$name], ['name' => $name]);
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function getNames()
    {
        $this->load();

        return \array_keys($this->commands);
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function has($name)
    {
        $this->load();

        return isset($this->commands[$name]);
    }

    /**
     * Load the registered commands into configuration.
     *
     * @throws \ReflectionException
     * @throws \LowlyPHP\Exception\ConfigException
     */
    private function load()
    {
        if (empty($this->commands)) {
            $schedule = (array) $this->app->config(static::COMMAND_CONFIG);

            foreach ($schedule as $name => $class) {
                $inspection = new \ReflectionClass($class);

                if (!$inspection->isSubclassOf(Command::class)) {
                    throw new \InvalidArgumentException(
                        \sprintf('Registered command "%s" must be an instance of %s.', $class, Command::class)
                    );
                }

                $this->commands[$name] = $class;
            }
        }
    }
}
