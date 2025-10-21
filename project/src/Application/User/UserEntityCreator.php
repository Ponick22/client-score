<?php

namespace App\Application\User;

use App\Application\User\DTO\UserCreateData;
use App\Application\User\Service\UserPasswordChanger;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Factory\UserEntityFactoryInterface;

readonly class UserEntityCreator
{
    public function __construct(
        private UserEntityFactoryInterface $factory,
        private UserPasswordChanger        $passwordChanger,
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
            $this->passwordChanger->change($entity, $data->getPassword());
        }

        $this->entityManager->persist($entity);

        return $entity;
    }
}
