<?php

namespace App\Application\Client\Connector\Command\EntityUpdate;

use App\Application\Client\ClientEntityGetter;
use App\Application\Client\Connector\Command\EntityUpdate\Contract\ClientEntityUpdateDataInterface;
use App\Application\Client\Connector\Command\EntityUpdate\Validator\ClientEntityUpdateValidator;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Application\Exception\ValidationException;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\PhoneOperator\PhoneOperatorGetterInterface;

readonly class ClientEntityUpdateCommand
{
    public function __construct(
        private ClientEntityGetter           $getter,
        private ClientEntityUpdateValidator  $validator,
        private PhoneOperatorGetterInterface $phoneOperatorGetter,
        private ClientScoreCalculating       $clientScoreCalculating,
        private EntityManagerInterface       $entityManager,
    ) {}

    /**
     * @throws ClientEntityNotFoundException
     * @throws ValidationException
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     */
    public function execute(ClientEntityUpdateDataInterface $data): ClientOutputData
    {
        $client = $this->getter->get($data->getId());

        $errors = $this->validator->validate($client, $data);

        if (!$errors->isValid()) {
            throw new ValidationException($errors);
        }

        $profile = $client->getProfile();

        $oldProfilePhone = (string)$profile->getPhone();

        $profile
            ->setEmail($data->getEmail())
            ->setPhone($data->getPhone())
            ->setFirstName($data->getFirstName())
            ->setLastName($data->getLastName());

        $client
            ->setEducation($data->getEducation())
            ->setConsentPersonalData($data->getConsentPersonalData());

        if ($oldProfilePhone !== (string)$data->getPhone() or !$client->getPhoneOperator()) {
            $client->setPhoneOperator($this->phoneOperatorGetter->get($data->getPhone()));
        }

        $client->setScore($this->clientScoreCalculating->calculate($client)->getScore());

        $this->entityManager->flush();

        return new ClientOutputData($client);
    }
}
