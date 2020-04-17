<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Server;

use Relay\Console\Application;

/**
 * Console command utility for web server executable location services.
 */
class ServiceLocator
{
    /**
     * Locate the web server executable path.
     *
     * @return string
     * @throws \Exception
     */
    public function locate() : string
    {
        $path = \rtrim(Application::getBasePath(), DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'vendor'
            . DIRECTORY_SEPARATOR . 'bin'
            . DIRECTORY_SEPARATOR . 'webserver';

        if (!\file_exists($path)) {
            throw new \Exception('Failed to locate web server executable.');
        }

        return $path;
    }
}
