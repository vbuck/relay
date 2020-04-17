<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api;

/**
 * Unique ID generator interface.
 */
interface GeneratorInterface
{
    /**
     * Generate a unique ID.
     *
     * @param string $prefix
     * @param string $suffix
     * @param int $length
     * @return string
     */
    public function generate(string $prefix = '', string $suffix = '', int $length = 0) : string;
}
