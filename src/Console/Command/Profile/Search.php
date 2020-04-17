<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Profile;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Console\Command\AbstractSearchCommand;
use Relay\Console\ConfirmationQuestionFactory;
use Relay\Console\TableFactory;
use Relay\Data\ProfileRepository;

class Search extends AbstractSearchCommand
{
    /**
     * {@inheritdoc}
     *
     * @param ProfileRepository $repository
     */
    public function __construct(
        ProfileRepository $repository,
        RepositorySearchFactory $repositorySearchFactory,
        TableFactory $tableFactory,
        ConfirmationQuestionFactory $confirmationQuestionFactory,
        ?string $name = null
    ) {
        parent::__construct(
            $repository,
            $repositorySearchFactory,
            $tableFactory,
            $confirmationQuestionFactory,
            $name
        );
    }

    /**
     * @inheritdoc
     */
    protected function getTableHeaders() : array
    {
        return ['ID', 'Name', 'Status'];
    }

    /**
     * {@inheritdoc}
     *
     * @param EntityInterface|\Relay\Data\Profile $record
     */
    protected function getRecordColumns(EntityInterface $record) : array
    {
        return [
            $record->getEntityId(),
            $record->getName(),
            $record->getStatus() ? 'Active' : 'Inactive',
        ];
    }
}
