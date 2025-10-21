<?php

namespace App\Domain\EntityManager;

use App\Domain\EntityManager\Exception\EntityManagerException;

interface EntityManagerInterface
{
    /**
     * @throws EntityManagerException
     */
    public function persist(object $entity): self;

    /**
     * @throws EntityManagerException
     */
    public function flush(): self;

    /**
     * @throws EntityManagerException
     */
    public function remove(object $entity): self;

    /**
     * @throws EntityManagerException
     */
    public function clear(): self;

    /**
     * @throws EntityManagerException
     */
    public function beginTransaction(): void;

    /**
     * @throws EntityManagerException
     */
    public function commitTransaction(): void;

    /**
     * @throws EntityManagerException
     */
    public function rollbackTransaction(): void;
}
