<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Workflow;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use LowlyPHP\Service\Api\FilterInterface;
use LowlyPHP\Service\Resource\EntityInterface;
use Relay\Api\RequestInterface;
use Relay\Api\Workflow\Action\ProcessorInterface as ActionProcessorInterface;
use Relay\Api\Workflow\Action\ResultInterface;
use Relay\Api\Workflow\ProcessorInterface;
use Relay\Data\Action;
use Relay\Workflow\Action\ResultFactory;
use Relay\Data\ActionRepository;
use Relay\Data\Workflow;

/**
 * Implementation for {@see ProcessorInterface}.
 */
class Processor implements ProcessorInterface
{
    /** @var ActionProcessorInterface */
    private $actionProcessor;

    /** @var ActionRepository */
    private $actionRepository;

    /** @var RepositorySearchFactory */
    private $repositorySearchFactory;

    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param ActionRepository $actionRepository
     * @param ActionProcessorInterface $actionProcessor
     * @param RepositorySearchFactory $repositorySearchFactory
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        ActionRepository $actionRepository,
        ActionProcessorInterface $actionProcessor,
        RepositorySearchFactory $repositorySearchFactory,
        ResultFactory $resultFactory
    ) {
        $this->actionRepository = $actionRepository;
        $this->actionProcessor = $actionProcessor;
        $this->repositorySearchFactory = $repositorySearchFactory;
        $this->resultFactory = $resultFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function process(Workflow $workflow, RequestInterface $request) : ResultInterface
    {
        /** @var Action[] $actions */
        $actions = $this->getActions($workflow->getSequence());

        /** @var ResultInterface $result */
        $result = $this->resultFactory->create();

        /** @var Action $action */
        foreach ($actions as $action) {
            $result = $this->actionProcessor->process($action, $request, $result);

            if (!$result->getSuccessFlag()) {
                break;
            }
        }

        return $result;
    }

    /**
     * Collect the workflow ordered action sequence.
     *
     * @param int[] $sequence
     * @return array
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    private function getActions(array $sequence) : array
    {
        /** @var \LowlyPHP\Service\Api\RepositorySearchInterface $criteria */
        $criteria = $this->repositorySearchFactory->create();
        $criteria->addFilter(
            EntityInterface::ID,
            \implode(',', $sequence),
            FilterInterface::COMPARATOR_IN_SET
        );

        $actions = $this->actionRepository->list($criteria);

        \usort(
            $actions,
            function (Action $a, Action $b) use ($sequence) {
                return \strcmp(
                    (string) \array_search($a->getEntityId(), $sequence),
                    (string) \array_search($b->getEntityId(), $sequence)
                );
            }
        );

        return $actions;
    }
}
