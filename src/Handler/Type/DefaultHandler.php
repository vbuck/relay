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
 * Default handler. Used as a fallback for endpoint acknowledgement.
 */
class DefaultHandler implements HandlerInterface
{
    const TYPE_CODE = 'default';

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
        return $this->resultFactory->create(
            self::TYPE_CODE,
            'No registered handlers to accept this request.',
            false,
            $this->generator->generate()
        );
    }
}
