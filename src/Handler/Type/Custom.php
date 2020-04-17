<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Handler\Type;

use Relay\Api\Handler\ResultInterface;
use Relay\Api\HandlerInterface;
use Relay\Api\RequestInterface;
use Relay\Handler\ReferenceIdGenerator;
use Relay\Handler\ResultFactory;

/**
 * Custom handler. Used to output results from applied actions in a workflow.
 */
class Custom implements HandlerInterface
{
    const TYPE_CODE = 'custom';

    /** @var ReferenceIdGenerator */
    private $generator;

    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param ResultFactory $resultFactory
     * @param ReferenceIdGenerator $generator
     */
    public function __construct(ResultFactory $resultFactory, ReferenceIdGenerator $generator)
    {
        $this->resultFactory = $resultFactory;
        $this->generator = $generator;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function handle(RequestInterface $request) : ResultInterface
    {
        $state = (bool) $request->getParameter('state');
        $message = (string) $request->getParameter('message');
        $referenceId = $request->getParameter('reference_id') ?: $this->generator->generate();
        $timestamp = $request->getParameter('timestamp') ?: \time();

        return $this->resultFactory->create(
            self::TYPE_CODE,
            $message,
            $state,
            (string) $referenceId,
            (int) $timestamp
        );
    }
}
