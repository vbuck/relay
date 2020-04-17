<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Workflow;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use Relay\Console\Command\AbstractDeleteCommand;
use Relay\Data\WorkflowRepository;

class Delete extends AbstractDeleteCommand
{
    /**
     * {@inheritdoc}
     *
     * @param WorkflowRepository $repository
     */
    public function __construct(WorkflowRepository $repository, ?string $name = null) {
        parent::__construct($repository, $name);
    }
}
