<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay;

use LowlyPHP\Provider\Api\RepositorySearchFactory;
use Relay\Api\Handler\ResultInterface;
use Relay\Api\ProfilePoolInterface;
use Relay\Api\RequestInterface;
use Relay\Api\Workflow\ProcessorInterface;
use Relay\Data\Profile;
use Relay\Data\ProfileRepository;
use Relay\Data\WorkflowRepository;

/**
 * Relay profile pool processor.
 */
class ProfilePool implements ProfilePoolInterface
{
    /** @var ProfileRepository */
    private $profileRepository;

    /** @var RepositorySearchFactory */
    private $repositorySearchFactory;

    /** @var ProcessorInterface */
    private $workflowProcessor;

    /** @var WorkflowRepository */
    private $workflowRepository;

    /**
     * @param ProfileRepository $profileRepository
     * @param WorkflowRepository $workflowRepository
     * @param ProcessorInterface $workflowProcessor
     * @param RepositorySearchFactory $repositorySearchFactory
     */
    public function __construct(
        ProfileRepository $profileRepository,
        WorkflowRepository $workflowRepository,
        ProcessorInterface $workflowProcessor,
        RepositorySearchFactory $repositorySearchFactory
    ) {
        $this->profileRepository = $profileRepository;
        $this->workflowRepository = $workflowRepository;
        $this->workflowProcessor = $workflowProcessor;
        $this->repositorySearchFactory = $repositorySearchFactory;
    }

    /**
     * @inheritdoc
     */
    public function process(RequestInterface $request) : array
    {
        /** @var ResultInterface[] $results */
        $results = [];

        /** @var \LowlyPHP\Service\Api\RepositorySearchInterface $criteria */
        $criteria = $this->repositorySearchFactory->create();
        $criteria->addFilter(Profile::STATUS, (string) Profile::STATUS_ENABLED);

        /** @var Profile $profile */
        foreach ($this->profileRepository->list($criteria) as $profile) {
            /** @var \Relay\Data\Workflow $workflow */
            $workflow = $this->workflowRepository->read((string) $profile->getWorkflowId());

            /**
             * Requests are passed to processors in their original form.
             * They are cloned from the original because processors are allowed to modify requests.
             */
            $workflowRequest = clone $request;

            /** @var \Relay\Api\Workflow\Action\ResultInterface $actionResult */
            $actionResult = $this->workflowProcessor->process($workflow, $workflowRequest);
            $results[] = $actionResult;

            if (!$actionResult->getSuccessFlag()) {
                break;
            }
        }

        return $results;
    }
}
