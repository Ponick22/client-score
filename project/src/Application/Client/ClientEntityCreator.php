<?php

namespace App\Application\Client;

use App\Application\Client\DTO\ClientCreateData;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Factory\ClientEntityFactoryInterface;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\PhoneOperator\PhoneOperatorGetterInterface;

readonly class ClientEntityCreator
{
    public function __construct(
        private ClientEntityFactoryInterface $factory,
        private PhoneOperatorGetterInterface $operatorGetter,
        private ClientScoreCalculating       $scoreCalculating,
        private EntityManagerInterface       $entityManager,
    ) {}

    /**
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     */
    public function create(ClientCreateData $data): ClientEntityInterface
    {
        $entity = $this->factory->create();

        $entity
            ->setProfile($data->getProfile())
            ->setEducation($data->getEducation())
            ->setConsentPersonalData($data->getConsentPersonalData())
            ->setPhoneOperator($this->operatorGetter->get($data->getProfile()->getPhone()));

        $entity->setScore($this->scoreCalculating->calculate($entity)->getScore());

        $this->entityManager->persist($entity);

        return $entity;
    }
}
