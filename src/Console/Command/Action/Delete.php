<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Action;

use Relay\Console\Command\AbstractDeleteCommand;
use Relay\Data\ActionRepository;

class Delete extends AbstractDeleteCommand
{
    /**
     * {@inheritdoc}
     *
     * @param ActionRepository $repository
     */
    public function __construct(ActionRepository $repository, ?string $name = null) {
        parent::__construct($repository, $name);
    }
}
