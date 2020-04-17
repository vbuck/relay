<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Data;

use LowlyPHP\Provider\Resource\StorageFactory;
use LowlyPHP\Service\Resource\EntityInterface;
use LowlyPHP\Service\Resource\EntityManagerInterface;
use LowlyPHP\Service\Api\RepositoryInterface;
use LowlyPHP\Service\Api\RepositorySearchInterface;
use LowlyPHP\Service\Resource\Storage\SchemaMapperInterface;
use Relay\Api\Data\StatisticEntityInterface;
use Relay\Api\Data\StatisticRepositoryInterface;

/**
 * System variable CRUD implementation for {@see \LowlyPHP\Service\Api\RepositoryInterface}.
 */
class SystemVariableRepository implements RepositoryInterface, StatisticRepositoryInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var SchemaMapperInterface */
    private $schemaMapper;

    /** @var StatisticEntityFactory */
    private $statisticEntityFactory;

    /** @var StorageFactory */
    private $storageFactory;

    /** @var SystemVariableFactory */
    private $systemVariableFactory;

    /**
     * @param SystemVariableFactory $systemVariableFactory
     * @param EntityManagerInterface $entityManager
     * @param StorageFactory $storageFactory
     * @param SchemaMapperInterface $schemaMapper
     * @param StatisticEntityFactory $statisticEntityFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        SystemVariableFactory $systemVariableFactory,
        EntityManagerInterface $entityManager,
        StorageFactory $storageFactory,
        SchemaMapperInterface $schemaMapper,
        StatisticEntityFactory $statisticEntityFactory
    ) {
        $this->systemVariableFactory = $systemVariableFactory;
        $this->entityManager = $entityManager;
        $this->storageFactory = $storageFactory;
        $this->schemaMapper = $schemaMapper;
        $this->statisticEntityFactory = $statisticEntityFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @return SystemVariable
     * @throws \LowlyPHP\Exception\StorageReadException
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function create(array $data = []) : EntityInterface
    {
        /** @var SystemVariable $entity */
        $entity = $this->systemVariableFactory->create();

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
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\InvalidEntityException
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
     * @return SystemVariable
     * @throws \LowlyPHP\Exception\ConfigException
     */
    public function read(string $id, int $scopeId = null) : EntityInterface
    {
        /** @var SystemVariable $entity */
        $entity = $this->systemVariableFactory->create();
        $entity->setEntityId((int) $id);

        $this->entityManager->hydrate($entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function stat(RepositorySearchInterface $criteria) : StatisticEntityInterface
    {
        /** @var SystemVariable $reference */
        $reference = $this->systemVariableFactory->create();

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
