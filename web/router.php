<?php

declare(strict_types=1);

/**
 * Simple router for the built-in web server.
 *
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */

namespace LowlyPHP\Web;

require \dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Relay\Application as ApiApp;
use Relay\Web\Application as WebApp;

if (\php_sapi_name() === 'cli-server') {
    $request = \trim($_SERVER['REQUEST_URI'], '/');

    if (!empty($request) && !\preg_match('/\.php$/', $request)) {
        \header('Content-type: ' . get_mime_type(\basename($request)));

        if (($file = try_file($request))) {
            echo \file_get_contents($file);
            return;
        }

        /** @var \LowlyPHP\Service\ApplicationInterface $app */
        $app = new ApiApp();
    } else {
        /** @var \LowlyPHP\Service\ApplicationInterface $app */
        $app = new WebApp();
    }

    $app->run();
    return;
}

\http_response_code(404);

function try_file($path) : string
{
    return \is_file(__DIR__ . DIRECTORY_SEPARATOR . $path) ? __DIR__ . DIRECTORY_SEPARATOR . $path : '';
}

function get_mime_type($name) : string
{
    $map = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'text/json',
        'html' => 'text/html',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'svg' => 'image/svg',
    ];

    $components = \explode('.', $name);
    $extension = \strtolower(\end($components));

    return $map[$extension] ?? 'text/plain';
}
