<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Setup;

use LowlyPHP\Provider\Resource\Storage\Driver\Pdo\Mysql;

class ConnectionWrapper extends Mysql
{
    /**
     * PDO connection accessor.
     *
     * @return \PDO
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function getConnection() : \PDO
    {
        $this->connect();
        return $this->connection;
    }

    /**
     * Get the configured database name.
     *
     * @return string
     */
    public function getDatabase() : string
    {
        return (string) $this->config[self::CONFIG_NAME];
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to connect without database or config validation.
     */
    protected function connect() : void
    {
        $this->connection = new \PDO(
            sprintf(
                'mysql:host=%s;port=%d;charset=%s',
                $this->config[self::CONFIG_HOST],
                (int) $this->config[self::CONFIG_PORT],
                $this->config[self::CONFIG_CHARSET]
            ),
            $this->config[self::CONFIG_USER],
            $this->config[self::CONFIG_PASS]
        );

        $this->connection->setAttribute(\PDO::ATTR_PERSISTENT, true);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}
