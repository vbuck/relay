<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Tracer\Observer;

use LowlyPHP\ApplicationManager;
use LowlyPHP\Service\ApplicationInterface;
use LowlyPHP\Service\Resource\SerializerInterface;
use Relay\Api\Tracer\ObserverInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * CLI-based observer implementation for {@see ObserverInterface}.
 */
class Console implements ObserverInterface
{
    const TYPE = 'console';

    /** @var int */
    private $entityId;

    /** @var array */
    private $metadata;

    /** @var string */
    private $resourceName;

    /** @var SerializerInterface */
    private $serializer;

    /** @var array */
    private $tags;

    /** @var ConsoleOutput */
    private $output;

    /**
     * @param string|null $resourceName
     * @param array $tags
     * @param array $metadata
     * @param ConsoleOutput|null $output
     * @param SerializerInterface|null $serializer
     * @param ApplicationInterface|null $app
     * @throws \LowlyPHP\Exception\ConfigException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) Resource name is determined by the output stream wrapper.
     */
    public function __construct(
        string $resourceName = null,
        array $tags = [],
        array $metadata = [],
        ConsoleOutput $output = null,
        SerializerInterface $serializer = null,
        ApplicationInterface $app = null
    ) {
        $this->tags = $tags ?? [];
        $this->metadata = (array) $metadata;
        $app = $app ?: ApplicationManager::getInstance();
        $this->output = $output ?: $app->getObject(ConsoleOutput::class);
        $this->serializer = $serializer ?: $app->getObject(SerializerInterface::class);
        $streamInfo = (array) \stream_get_meta_data($output->getStream());
        $this->resourceName = $streamInfo['uri'] ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function export() : array
    {
        return [
            static::ID => $this->getEntityId(),
            static::ORIGIN_ID => $this->getOriginId(),
            static::METADATA => $this->serializer->serialize($this->getMetadata()),
            static::RESOURCE_NAME => $this->getResourceName(),
            static::TAGS => $this->serializer->serialize($this->getTags()),
            static::TYPE => $this->getType(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId() : int
    {
        return (int) $this->entityId;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata() : array
    {
        return (array) $this->metadata;
    }

    /**
     * @inheritdoc
     */
    public function getOriginId() : string
    {
        $components = [
            \getmypid(),
            \gethostname(),
            $this->getResourceName(),
            $this->tags,
            $this->metadata,
        ];

        return \sha1($this->serializer->serialize($components));
    }

    /**
     * @inheritdoc
     */
    public function getResourceName() : string
    {
        return (string) $this->resourceName;
    }

    /**
     * @inheritdoc
     */
    public function getTags() : array
    {
        return (array) $this->tags;
    }

    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return (string) static::TYPE;
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return $this->serializer->serialize(
            [
                'resourceName' => $this->getResourceName(),
                'tags' => $this->getTags(),
                'metadata' => $this->getMetadata(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId(int $id) : void
    {
        $this->entityId = $id;
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $data = (array) $this->serializer->unserialize($serialized);

        if (isset($data['resourceName'])) {
            $this->resourceName = (string) $data['resourceName'];
        }

        if (isset($data['tags'])) {
            $this->tags = (array) $data['tags'];
        }

        if (isset($data['metadata'])) {
            $this->metadata = (array) $data['metadata'];
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param \SplSubject|\Relay\Api\Tracer\DataInterface $subject
     */
    public function update(\SplSubject $subject)
    {
        $this->output->writeln($subject->getMessage());
    }
}
