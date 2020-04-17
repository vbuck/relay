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
use Relay\Data\SystemVariableRegistry;
use Relay\Handler\ReferenceIdGenerator;
use Relay\Handler\ResultFactory;
use Relay\Handler\Type\GitHub\Authorization;

/**
 * GitHub web hook handler.
 */
class GitHub implements HandlerInterface
{
    const TYPE_CODE = 'github';
    const SYSTEM_VAR_TOKEN = 'github.token';
    const ENV_VAR_TOKEN = 'GITHUB_TOKEN';

    /** @var Authorization */
    private $authorization;

    /** @var ReferenceIdGenerator */
    private $generator;

    /** @var ResultFactory */
    private $resultFactory;

    /** @var SystemVariableRegistry */
    private $systemVariableRegistry;

    /**
     * @param ResultFactory $resultFactory
     * @param Authorization $authorization
     * @param ReferenceIdGenerator $generator
     * @param SystemVariableRegistry $systemVariableRegistry
     */
    public function __construct(
        ResultFactory $resultFactory,
        Authorization $authorization,
        ReferenceIdGenerator $generator,
        SystemVariableRegistry $systemVariableRegistry
    ) {
        $this->resultFactory = $resultFactory;
        $this->authorization = $authorization;
        $this->generator = $generator;
        $this->systemVariableRegistry = $systemVariableRegistry;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function handle(RequestInterface $request) : ResultInterface
    {
        $signature = $request->getHeader('X-Hub-Signature');
        $input = $request->getBody();

        if (!$signature) {
            return $this->resultFactory->create(
                self::TYPE_CODE,
                'Invalid signature',
                false,
                $this->generator->generate()
            );
        }

        if (!$this->authorization->authorize($this->getToken(), $signature, $input)) {
            return $this->resultFactory->create(
                self::TYPE_CODE,
                'Authorization failed.',
                false,
                $this->generator->generate()
            );
        }

        return $this->resultFactory->create(
            self::TYPE_CODE,
            $request->getBody(),
            true,
            $this->generator->generate()
        );
    }

    /**
     * Get the configured token for authorization.
     *
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     * @return string
     */
    private function getToken() : string
    {
        return (string) (
            $_ENV[static::ENV_VAR_TOKEN] ?? $this->systemVariableRegistry->get(static::SYSTEM_VAR_TOKEN)
        );
    }
}
