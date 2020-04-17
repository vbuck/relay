<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\SystemVariable;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use Relay\Console\Command\AbstractStatCommand;
use Relay\Data\SystemVariableRepository;

class Stat extends AbstractStatCommand
{
    /**
     * {@inheritdoc}
     *
     * @param SystemVariableRepository $repository
     */
    public function __construct(
        SystemVariableRepository $repository,
        RepositorySearchFactory $repositorySearchFactory,
        ?string $name = null
    ) {
        parent::__construct($repository, $repositorySearchFactory, $name);
    }
}
