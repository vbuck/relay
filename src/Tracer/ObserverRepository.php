<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Tracer;

use LowlyPHP\Provider\Resource\StorageFactory;
use LowlyPHP\Service\Api\RepositorySearchInterface;
use LowlyPHP\Service\Resource\EntityInterface;
use LowlyPHP\Service\Resource\EntityManagerInterface;
use LowlyPHP\Service\Resource\EntityMapperInterface;
use LowlyPHP\Service\Resource\Storage\SchemaMapperInterface;
use Relay\Api\Data\StatisticEntityInterface;
use Relay\Api\Data\StatisticRepositoryInterface;
use Relay\Api\Tracer\ObserverInterface;
use Relay\Api\Tracer\ObserverRepositoryInterface;
use Relay\Data\StatisticEntityFactory;

/**
 * Implementation for {@see ObserverStorageInterface} instances.
 */
class ObserverRepository extends \SplObjectStorage implements ObserverRepositoryInterface, StatisticRepositoryInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EntityMapperInterface */
    private $entityMapper;

    /** @var ObserverFactory */
    private $observerFactory;

    /** @var SchemaMapperInterface */
    private $schemaMapper;

    /** @var StatisticEntityFactory */
    private $statisticEntityFactory;

    /** @var StorageFactory */
    private $storageFactory;

    /**
     * @param ObserverFactory $observerFactory
     * @param EntityManagerInterface $entityManager
     * @param EntityMapperInterface $entityMapper
     * @param StorageFactory $storageFactory
     * @param SchemaMapperInterface $schemaMapper
     * @param StatisticEntityFactory $statisticEntityFactory
     */
    public function __construct(
        ObserverFactory $observerFactory,
        EntityManagerInterface $entityManager,
        EntityMapperInterface $entityMapper,
        StorageFactory $storageFactory,
        SchemaMapperInterface $schemaMapper,
        StatisticEntityFactory $statisticEntityFactory
    ) {
        $this->observerFactory = $observerFactory;
        $this->entityManager = $entityManager;
        $this->entityMapper = $entityMapper;
        $this->storageFactory = $storageFactory;
        $this->schemaMapper = $schemaMapper;
        $this->statisticEntityFactory = $statisticEntityFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\EntityExistsException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageWriteException
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function addAll($storage)
    {
        parent::addAll($storage);

        /** @var ObserverInterface|object $observer */
        foreach ($storage as $observer) {
            if ($observer instanceof ObserverInterface
                && !$observer->getEntityId()
            ) {
                /** @var ObserverInterface $result */
                $result = $this->create($observer->export());
                $observer->setEntityId($result->getEntityId());
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param ObserverInterface|object|object $object
     * @throws \LowlyPHP\Exception\EntityExistsException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageWriteException
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function attach($object, $data = null)
    {
        parent::attach($object, $data);

        if ($object instanceof ObserverInterface
            && !$object->getEntityId()) {
            /** @var ObserverInterface $result */
            $result = $this->create($object->export());
            $object->setEntityId($result->getEntityId());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function create(array $data = []) : EntityInterface
    {
        if (empty($data[ObserverInterface::TYPE])) {
            throw new \InvalidArgumentException('Observer type property must be specified.');
        }

        /** @var ObserverInterface $entity */
        $entity = $this->observerFactory->create($data[ObserverInterface::TYPE]);

        $this->entityManager->hydrate($entity, $data);
        $this->entityManager->persist($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function delete(EntityInterface $entity) : void
    {
        $this->entityManager->remove($entity);
    }

    /**
     * {@inheritdoc}
     *
     * @param ObserverInterface|object $object
     * @throws \LowlyPHP\Exception\StorageWriteException
     */
    public function detach($object)
    {
        parent::detach($object);

        if ($object instanceof ObserverInterface
            && $object->getEntityId()
        ) {
            $this->delete($object);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function list(RepositorySearchInterface $criteria) : array
    {
        $results = [];

        /** @var int $id */
        foreach ($this->stat($criteria)->getIds() as $id) {
            $results[] = $this->read((string) $id);
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function read(string $id, int $scopeId = null) : EntityInterface
    {
        /** @var ObserverInterface $entity */
        $reference = $this->observerFactory->create(ObserverInterface::class);
        $reference->setEntityId((int) $id);

        $this->entityManager->hydrate($reference);

        $typedInstance = $this->observerFactory->create($reference->getType());
        $this->entityMapper->map($reference->export(), $typedInstance);

        return $typedInstance;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\StorageWriteException
     */
    public function removeAll($storage)
    {
        parent::removeAll($storage);

        /** @var ObserverInterface|object $observer */
        foreach ($storage as $observer) {
            if ($observer instanceof ObserverInterface
                && $observer->getEntityId()
            ) {
                $this->delete($observer);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\StorageWriteException
     */
    public function removeAllExcept($storage)
    {
        parent::removeAllExcept($storage);

        /** @var ObserverInterface|object $observer */
        foreach ($storage as $observer) {
            if ($observer instanceof ObserverInterface
                && $observer->getEntityId()
                && !$this->contains($observer)
            ) {
                $this->delete($observer);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function stat(RepositorySearchInterface $criteria) : StatisticEntityInterface
    {
        /** @var ObserverInterface $reference */
        $reference = $this->observerFactory->create(ObserverInterface::class);

        /** @var \LowlyPHP\Service\Resource\StorageInterface $storage */
        $storage = $this->storageFactory->create(
            $this->schemaMapper->map($reference)
        );

        $records = $storage->query($criteria->getFilters(), $criteria->getPage(), $criteria->getLimit());
        $ids = \array_map(
            function ($record) {
                return $record[EntityInterface::ID];
            },
            $records
        );

        return $this->statisticEntityFactory->create($ids);
    }

    /**
     * @inheritdoc
     */
    public function update(EntityInterface $entity) : void
    {
        $this->entityManager->flush($entity);
    }
}
