<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Profile;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use Relay\Console\Command\AbstractInfoCommand;
use Relay\Data\ProfileRepository;

class Info extends AbstractInfoCommand
{
    /**
     * {@inheritdoc}
     *
     * @param ProfileRepository $repository
     */
    public function __construct(
        ProfileRepository $repository,
        RepositorySearchFactory $repositorySearchFactory,
        ?string $name = null
    ) {
        parent::__construct($repository, $repositorySearchFactory, $name);
    }
}
