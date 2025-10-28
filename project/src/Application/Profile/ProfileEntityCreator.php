<?php

namespace App\Application\Profile;

use App\Application\Profile\DTO\ProfileCreateData;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Factory\ProfileEntityFactoryInterface;

readonly class ProfileEntityCreator
{
    public function __construct(
        private ProfileEntityFactoryInterface $factory,
        private EntityManagerInterface        $entityManager,
    ) {}

    /**
     * @throws EntityManagerException
     */
    public function create(ProfileCreateData $data): ProfileEntityInterface
    {
        $entity = $this->factory->create();

        $entity
            ->setUser($data->getUser())
            ->setEmail($data->getEmail())
            ->setPhone($data->getPhone())
            ->setFirstName($data->getFirstName())
            ->setLastName($data->getLastName());

        $this->entityManager->persist($entity);

        return $entity;
    }
}
