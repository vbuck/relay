<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Api\Tracer;

use LowlyPHP\Service\Api\RepositoryInterface;

/**
 * Tracer process observer repository. Used to persist observers across requests.
 */
interface ObserverRepositoryInterface extends RepositoryInterface
{

}
