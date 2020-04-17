<?php

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Console\Command\Action;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Console\Command\AbstractSearchCommand;
use Relay\Console\ConfirmationQuestionFactory;
use Relay\Console\TableFactory;
use Relay\Data\ActionRepository;

class Search extends AbstractSearchCommand
{
    /**
     * {@inheritdoc}
     *
     * @param ActionRepository $repository
     */
    public function __construct(
        ActionRepository $repository,
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
        return ['ID', 'Description'];
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
            $record->getDescription(),
        ];
    }
}
