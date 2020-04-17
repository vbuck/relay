<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Setup;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use LowlyPHP\Provider\Resource\Storage\SchemaFactory;

/**
 * Factory for {@see ConnectionWrapper} instances.
 */
class ConnectionWrapperFactory
{
    const DEFAULT_CONNECTION_NAME = 'default';

    /** @var ApplicationManager */
    private $app;

    /** @var SchemaFactory */
    private $schemaFactory;

    /**
     * @param SchemaFactory $schemaFactory
     * @param ApplicationManager|null $app
     * @codeCoverageIgnore
     */
    public function __construct(SchemaFactory $schemaFactory, ApplicationManager $app = null)
    {
        $this->app = $app ?? ApplicationManager::getInstance();
        $this->schemaFactory = $schemaFactory;
    }

    /**
     * Create a new connection wrapper object.
     *
     * @param string $name The target connection name.
     * @param null $source
     * @return ConnectionWrapper
     * @throws ConfigException
     */
    public function create(string $name = null, $source = null) : ConnectionWrapper
    {
        /**
         * Mock a schema to convey the name for this connection instance only.
         *
         * @var \LowlyPHP\Service\Resource\Storage\SchemaInterface $schema
         */
        $schema = $this->schemaFactory->create(
            $name ?: static::DEFAULT_CONNECTION_NAME,
            '',
            []
        );

        return $this->app->createObject(ConnectionWrapper::class, ['schema' => $schema]);
    }
}
