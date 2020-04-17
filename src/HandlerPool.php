<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay;

use LowlyPHP\ApplicationManager;
use Relay\Api\HandlerPoolInterface;
use Relay\Api\Handler\ResultInterface;
use Relay\Api\HandlerInterface;
use Relay\Api\RequestInterface;
use Relay\Handler\ReferenceIdGenerator;
use Relay\Handler\ResultFactory;

/**
 * Implementation for {@see HandlerPoolInterace}.
 */
class HandlerPool implements HandlerPoolInterface
{
    const DEFAULT_HANDLER_CODE = 'default';

    /** @var ApplicationManager */
    private $app;

    /** @var HandlerInterface[] */
    private $handlers;

    /** @var ReferenceIdGenerator */
    private $generator;

    /** @var ResultFactory */
    private $resultFactory;

    /**
     * @param ResultFactory $resultFactory
     * @param ReferenceIdGenerator $generator
     * @param ApplicationManager|null $app
     */
    public function __construct(
        ResultFactory $resultFactory,
        ReferenceIdGenerator $generator,
        ApplicationManager $app = null
    ) {
        $this->app = $app ?? ApplicationManager::getInstance();
        $this->resultFactory = $resultFactory;
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function handle(RequestInterface $request) : ResultInterface
    {
        /** @var HandlerInterface $handler */
        foreach ($this->get() as $code => $handler) {
            /** @var \Relay\Api\Handler\ResultInterface $result */
            $result = $handler->handle($request);

            if ($result->isSuccessful() || $code === static::DEFAULT_HANDLER_CODE) {
                return $result;
            }
        }

        return $this->resultFactory->create(
            'none',
            'No handler matched.',
            false,
            $this->generator->generate()
        );
    }

    /**
     * {@inheritdoc}
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function get(array $types = []) : array
    {
        if (empty($this->handlers)) {
            $this->load();
        }

        if (!empty($types)) {
            return \array_filter(
                $this->handlers,
                function ($key) use ($types) {
                    return \in_array($key, $types);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        return $this->handlers;
    }

    /**
     * Load all handlers from configuration.
     *
     * @throws \LowlyPHP\Exception\ConfigException
     */
    private function load() : void
    {
        $this->handlers = [];

        $types = (array) $this->app->config(
            \sprintf('providers.%s.types', HandlerInterface::class)
        );

        foreach ($types as $code => $type) {
            $handler = $this->app->getObject($type);

            if (!($handler instanceof HandlerInterface)) {
                throw new \InvalidArgumentException(
                    \sprintf('Invalid handler %s. Must implement %s', $type, HandlerInterface::class)
                );
            }

            $this->handlers[$code] = $handler;
        }

        // Force the default handler to the end of the stack
        \uksort(
            $this->handlers,
            function ($a, $b) {
                if (\strcasecmp($a, static::DEFAULT_HANDLER_CODE) === 0) {
                    return 1;
                } elseif (\strcasecmp($b, static::DEFAULT_HANDLER_CODE) === 0) {
                    return -1;
                }

                return -1;
            }
        );
    }
}
