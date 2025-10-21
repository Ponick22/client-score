<?php

namespace App\Application\Client\Connector\Command\EntityUpdate;

use App\Application\Client\ClientEntityGetter;
use App\Application\Client\ClientEntityUpdateService;
use App\Application\Client\Connector\Command\EntityUpdate\Contract\ClientEntityUpdateDataInterface;
use App\Application\Client\Connector\Command\EntityUpdate\Validator\ClientEntityUpdateValidator;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\DTO\ClientUpdateData;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Application\Exception\ValidationException;
use App\Application\PhoneOperator\Exception\PhoneOperatorException;
use App\Application\Profile\DTO\ProfileUpdateData;
use App\Application\Profile\ProfileEntityUpdateService;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;

readonly class ClientEntityUpdateCommand
{
    public function __construct(
        private ClientEntityGetter          $getter,
        private ClientEntityUpdateValidator $validator,
        private ProfileEntityUpdateService  $profileUpdateService,
        private ClientEntityUpdateService   $clientUpdateService,
        private EntityManagerInterface      $entityManager,
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

        $profileUpdateData = new ProfileUpdateData(
            $data->getEmail(),
            $data->getPhone(),
            $data->getFirstName(),
            $data->getLastName(),
        );

        $profile = $client->getProfile();

        $oldProfilePhone = $profile->getPhone();

        $this->profileUpdateService->update($profile, $profileUpdateData);

        $clientUpdateData = new ClientUpdateData(
            $data->getEducation(),
            $data->getConsentPersonalData(),
            ($oldProfilePhone !== $data->getPhone()) ? $data->getPhone() : null,
        );

        $this->clientUpdateService->update($client, $clientUpdateData);

        $this->entityManager->flush();

        return new ClientOutputData($client, $client->getProfile());
    }
}
