<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Handler;

use Relay\Api\GeneratorInterface;

/**
 * ID generator for handler reference requests.
 */
class ReferenceIdGenerator implements GeneratorInterface
{
    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function generate(string $prefix = '', string $suffix = '', int $length = 0) : string
    {
        return \strtoupper($prefix . $this->makeId() . $suffix);
    }

    /**
     * Create a new v4 UUID.
     *
     * @return string
     * @throws \Exception
     */
    private function makeId() : string
    {
        $data = \random_bytes(16);

        $data[6] = \chr(\ord($data[6]) & 0x0f | 0x40);
        $data[8] = \chr(\ord($data[8]) & 0x3f | 0x80);

        return \vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($data), 4));
    }
}
