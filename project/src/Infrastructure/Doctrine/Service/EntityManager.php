<?php

namespace App\Infrastructure\Doctrine\Service;

use App\Domain\EntityManager\EntityManagerInterface as AppEntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

readonly class EntityManager implements AppEntityManagerInterface
{
    /**
     * @param DoctrineEntityManager $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function persist(object $entity): static
    {
        try {
            $this->entityManager->persist($entity);
        } catch (Throwable $exception) {
            throw new EntityManagerException(
                sprintf('Failed to persist entity of class %s', get_class($entity)),
                EntityManagerException::ERROR_PERSIST,
                $exception
            );
        }

        return $this;
    }

    public function flush(): static
    {
        try {
            $this->entityManager->flush();
        } catch (Throwable $exception) {
            throw new EntityManagerException(
                'Failed to flush entity manager',
                EntityManagerException::ERROR_FLUSH,
                $exception
            );
        }

        return $this;
    }

    public function remove(object $entity): static
    {
        try {
            $this->entityManager->remove($entity);
        } catch (Throwable $exception) {
            throw new EntityManagerException(
                sprintf('Failed to delete entity of class %s', get_class($entity)),
                EntityManagerException::ERROR_REMOVE,
                $exception
            );
        }

        return $this;
    }

    public function clear(): static
    {
        try {
            $this->entityManager->clear();
        } catch (Throwable $exception) {
            throw new EntityManagerException(
                'Failed to clear entity manager',
                EntityManagerException::ERROR_CLEAR,
                $exception);
        }

        return $this;
    }

    public function beginTransaction(): void
    {
        try {
            $this->entityManager->beginTransaction();
        } catch (Throwable $exception) {
            throw new EntityManagerException(
                'Failed to begin transaction',
                EntityManagerException::ERROR_TRANSACTION_BEGIN,
                $exception);
        }
    }

    public function commitTransaction(): void
    {
        try {
            $this->entityManager->commit();
        } catch (Throwable $exception) {
            throw new EntityManagerException(
                'Failed to commit transaction',
                EntityManagerException::ERROR_TRANSACTION_COMMIT,
                $exception);
        }
    }

    public function rollbackTransaction(): void
    {
        try {
            $this->entityManager->rollback();
        } catch (Throwable $exception) {
            throw new EntityManagerException(
                'Failed to rollback transaction',
                EntityManagerException::ERROR_TRANSACTION_ROLLBACK,
                $exception);
        }
    }
}
