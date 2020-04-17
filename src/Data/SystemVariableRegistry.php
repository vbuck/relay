<?php

declare(strict_types=1);

/**
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @package   Relay
 * @license   MIT
 */

namespace Relay\Data;

use LowlyPHP\Provider\Api\RepositorySearchFactory;

/**
 * Relay system variable registry. Designed to hold persisted variables together with runtime variables.
 */
class SystemVariableRegistry
{
    /** @var bool */
    private $isLoaded;

    /** @var array */
    private $items;

    /** @var SystemVariableRepository */
    private $repository;

    /** @var RepositorySearchFactory */
    private $repositorySearchFactory;

    /**
     * @param SystemVariableRepository $repository
     * @param RepositorySearchFactory $repositorySearchFactory
     */
    public function __construct(SystemVariableRepository $repository, RepositorySearchFactory $repositorySearchFactory)
    {
        $this->repository = $repository;
        $this->repositorySearchFactory = $repositorySearchFactory;
        $this->items = [];
    }

    /**
     * Get a system variable value by name.
     *
     * @param string $name
     * @return string
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    public function get(string $name) : string
    {
        $this->load();

        return (string) ($this->items[$name][0] ?? '');
    }

    /**
     * Remove a system variable from the registry. Can optionally be removed from the repository.
     *
     * @param string $name
     * @param bool $commit
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\EntityExistsException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     * @throws \LowlyPHP\Exception\StorageWriteException
     */
    public function remove(string $name, bool $commit = false) : void
    {
        if (!empty($this->items[$name])) {
            if ($commit && !empty($this->items[$name][1])) {
                $this->commit(null, null, $this->items[$name][1], true);
            }

            unset($this->items[$name]);
        }
    }

    /**
     * Add a system variable to the registry. Can optionally be persisted beyond runtime (committed to repository).
     *
     * @param string $name
     * @param string $value
     * @param bool $persist
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\EntityExistsException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     * @throws \LowlyPHP\Exception\StorageWriteException
     */
    public function set(string $name, string $value = '', bool $persist = false) : void
    {
        $this->load();

        $data = \array_merge(
            [$value, 0],
            (array) ($this->items[$name] ?? [])
        );

        $this->items[$name] = $data;

        if ($persist) {
            $this->items[$name][1] = $this->commit($name, (string) $data[0], (int) $data[1], false);
        }
    }

    /**
     * Commit a record change to the repository. An ID must be provided to update an existing record.
     *
     * @param string|null $name
     * @param string|null $value
     * @param int|null $id
     * @param bool $remove
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\EntityExistsException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     * @throws \LowlyPHP\Exception\StorageWriteException
     * @return int
     */
    private function commit(string $name = null, string $value = null, int $id = null, bool $remove = false) : int
    {
        $entity = null;

        if ($id > 0) {
            /** @var \LowlyPHP\Service\Api\RepositorySearchInterface $criteria */
            $criteria = $this->repositorySearchFactory->create();
            $criteria->addFilter(SystemVariable::ID, (string) $id);

            /** @var SystemVariable|null $entity */
            $entity = \current($this->repository->list($criteria));
        }

        if (empty($entity)) {
            /** @var SystemVariable $entity */
            $entity = $this->repository->create(
                [
                    SystemVariable::ID => (int) $id,
                    SystemVariable::NAME => (string) $name,
                    SystemVariable::VALUE => (string) $value,
                ]
            );
        } else {
            $entity->setName((string) $name);
            $entity->setValue((string) $value);
        }

        if ($remove) {
            $this->repository->delete($entity);
        } else {
            $this->repository->update($entity);
        }

        return $entity->getEntityId();
    }

    /**
     * Load all system variables from the repository.
     *
     * @throws \LowlyPHP\Exception\ConfigException
     * @throws \LowlyPHP\Exception\InvalidEntityException
     * @throws \LowlyPHP\Exception\StorageReadException
     */
    private function load() : void
    {
        if (!$this->isLoaded) {
            $items = $this->repository->list($this->repositorySearchFactory->create());

            /** @var SystemVariable $item */
            foreach ($items as $item) {
                $this->items[$item->getName()] = [$item->getValue(), $item->getEntityId()];
            }

            $this->isLoaded = true;
        }
    }
}
