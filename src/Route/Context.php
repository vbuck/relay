<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Route;

/**
 * A context for route processing. Contexts provide prepared information about the request in a format which can be
 * understood by all route processors.
 */
class Context
{
    /** @var string */
    public $area;

    /** @var string */
    public $host;

    /** @var bool */
    public $secure;

    /**
     * @param string $host
     * @param string $area
     * @param bool $secure
     */
    public function __construct(
        string $host,
        string $area = 'default',
        bool $secure = false
    ) {
        $this->host = $host;
        $this->area = $area;
        $this->secure = $secure;
    }
}
