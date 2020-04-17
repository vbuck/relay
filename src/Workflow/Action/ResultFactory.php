<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow\Action;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Exception\ConfigException;
use Relay\Api\Workflow\Action\ResultInterface;

/**
 * Factory for {@see ResultInterface} instances.
 */
class ResultFactory
{
    /** @var ApplicationManager */
    private $app;

    /**
     * @param ApplicationManager|null $app
     * @codeCoverageIgnore
     */
    public function __construct(ApplicationManager $app = null)
    {
        $this->app = $app ?? ApplicationManager::getInstance();
    }

    /**
     * Create a new result object.
     *
     * @todo incomplete
     * @return ResultInterface
     * @throws ConfigException
     */
    public function create() : ResultInterface
    {
        return $this->app->createObject(ResultInterface::class, []);
    }
}
