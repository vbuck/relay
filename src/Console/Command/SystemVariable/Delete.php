<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\SystemVariable;

use Relay\Console\Command\AbstractDeleteCommand;
use Relay\Data\SystemVariableRepository;

class Delete extends AbstractDeleteCommand
{
    /**
     * {@inheritdoc}
     *
     * @param SystemVariableRepository $repository
     */
    public function __construct(SystemVariableRepository $repository, ?string $name = null) {
        parent::__construct($repository, $name);
    }
}
