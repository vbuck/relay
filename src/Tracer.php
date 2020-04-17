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
use Relay\Api\Tracer\ObserverRepositoryInterface;
use Relay\Api\TracerInterface;
use Relay\Tracer\DataFactory;

/**
 * Implementation for {@see TracerInterface}.
 */
class Tracer implements TracerInterface
{
    /** @var DataFactory */
    private $dataFactory;

    /** @var array */
    private $dataStructure;

    /** @var \SplObjectStorage */
    private $observerRepository;

    /** @var RepositorySearchFactory */
    private $repositorySearchFactory;

    /** @var array */
    private $stack;

    /**
     * @param ObserverRepositoryInterface $observerRepository
     * @param RepositorySearchFactory $repositorySearchFactory
     * @param DataFactory $dataFactory
     */
    public function __construct(
        ObserverRepositoryInterface $observerRepository,
        RepositorySearchFactory $repositorySearchFactory,
        DataFactory $dataFactory
    ) {
        $this->stack = [];
        $this->observerRepository = $observerRepository;
        $this->repositorySearchFactory = $repositorySearchFactory;
        $this->dataFactory = $dataFactory;
        $this->dataStructure = [
            'message' => '',
            'tags' => [],
            'context' => null,
            'backtrace' => [],
        ];
    }

    public function __destruct()
    {
        $this->detachAll();
    }

    /**
     * {@inheritdoc}
     *
     * @param \SplObserver|\Relay\Api\Tracer\ObserverInterface $observer
     */
    public function attach(\SplObserver $observer)
    {
        $this->observerRepository->attach($observer);
    }

    /**
     * {@inheritdoc}
     *
     * @param \SplObserver|\Relay\Api\Tracer\ObserverInterface $observer
     */
    public function detach(\SplObserver $observer)
    {
        $this->observerRepository->detach($observer);
    }

    /**
     * @inheritdoc
     */
    public function detachAll() : void
    {
        /** @var \SplObserver|\Relay\Api\Tracer\ObserverInterface $observer */
        foreach ($this->observerRepository as $observer) {
            $this->detach($observer);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function notify()
    {
        /** @var array $trace */
        $trace = (array) \array_shift($this->stack);

        if (empty($trace)) {
            return;
        }

        $trace = \array_merge($this->dataStructure, $trace);
        /** @var \Relay\Api\Tracer\DataInterface $subject */
        $subject = $this->dataFactory->create(...$trace);
        /** @var \Relay\Api\Tracer\ObserverInterface[] $observers */
        $observers = $this->observerRepository->list($this->repositorySearchFactory->create());

        /** @var \SplObserver|\Relay\Api\Tracer\ObserverInterface $observer */
        foreach ($observers as $observer) {
            /** @var array $subscribedTags */
            $subscribedTags = \method_exists($observer, 'getTags') ? $observer->getTags() : [];
            /** @var array $intersect */
            $intersect = \array_intersect($subscribedTags, (array) $trace['tags']);

            if (empty($subscribedTags) || !empty($intersect)) {
                $observer->update($subject);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function trace(string $message, array $tags = [], $context = null, array $backtrace = null) : void
    {
        $this->stack[] = \array_merge(
            $this->dataStructure,
            [
                'message' => $message,
                'tags' => $tags,
                'context' => $context,
                'backtrace' => $backtrace,
            ]
        );
    }
}
