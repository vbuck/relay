<?php

declare(strict_types=1);

/**
 * Web application container.
 *
 * @see       File router.php for request mapping to the main app.
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */

namespace Relay\Web;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Service\ApplicationInterface;

/**
 * Web API based application container. Creates a service for the frontend.
 */
class Application implements ApplicationInterface
{
    const AREA = 'frontend';

    /** @var ApplicationManager */
    private $appManager;

    /** @var boolean */
    private $debugMode;

    /**
     * {@inheritdoc}
     */
    public function run(ApplicationManager $appManager = null) : void
    {
        $this->appManager = $appManager ?? ApplicationManager::getInstance();
        $this->debugMode = $this->appManager->config('dev.debug_mode');

        if ($this->debugMode) {
            if (\class_exists(\Whoops\Run::class)) {
                $whoops = new \Whoops\Run();
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
                $whoops->register();
            }
        } else {
            \set_error_handler([$this, 'handleError']);
        }

        include 'index.php';
    }

    private function handleError($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        switch ($errno) {
            case E_USER_ERROR:
                \http_response_code(500);
                echo "Internal server error. This is a stub, a to-do item for implementation";
                exit(1);
            default:
                return false;
        }
    }
}

return new Application();
