<?php

namespace App\Application\User;

use App\Application\User\DTO\UserCreateData;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Factory\UserEntityFactoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;

readonly class UserEntityCreator
{
    public function __construct(
        private UserEntityFactoryInterface $factory,
        private PasswordHasherInterface    $hasher,
        private EntityManagerInterface     $entityManager,
    ) {}

    /**
     * @throws EntityManagerException
     */
    public function create(UserCreateData $data): UserEntityInterface
    {
        $entity = $this->factory->create();

        $entity
            ->setEmail($data->getEmail())
            ->setRoles($data->getRoles());

        if ($data->getPassword()) {
            $hashPassword = $this->hasher->hash($entity, $data->getPassword());
            $entity->setPassword($hashPassword);
        }

        $this->entityManager->persist($entity);

        return $entity;
    }
}
