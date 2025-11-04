<?php

namespace App\Application\Client\Connector\Command\EntityCreation;

use App\Application\Client\ClientEntityCreator;
use App\Application\Client\Connector\Command\EntityCreation\Contract\ClientEntityCreationDataInterface;
use App\Application\Client\Connector\Command\EntityCreation\Validator\ClientEntityCreationValidator;
use App\Application\Client\DTO\ClientCreateData;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Exception\ValidationException;
use App\Application\Profile\DTO\ProfileCreateData;
use App\Application\Profile\ProfileEntityCreator;
use App\Application\User\DTO\UserCreateData;
use App\Application\User\UserEntityCreator;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\User\Enum\UserRoleEnum;
use App\Domain\User\ValueObject\UserRoleEnumCollection;

readonly class ClientEntityCreationCommand
{
    public function __construct(
        private ClientEntityCreationValidator $validator,
        private UserEntityCreator             $userCreator,
        private ProfileEntityCreator          $profileCreator,
        private ClientEntityCreator           $clientCreator,
        private EntityManagerInterface        $entityManager,
    ) {}

    /**
     * @throws ValidationException
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     */
    public function execute(ClientEntityCreationDataInterface $data): ClientOutputData
    {
        $errors = $this->validator->validate($data);

        if (!$errors->isValid()) {
            throw new ValidationException($errors);
        }

        $userCreateData = new UserCreateData(
            $data->getUserEmail(),
            null,
            new UserRoleEnumCollection([UserRoleEnum::Client]),
        );

        $user = $this->userCreator->create($userCreateData);

        $profileCreateData = new ProfileCreateData(
            $user,
            $data->getProfileEmail(),
            $data->getPhone(),
            $data->getFirstName(),
            $data->getLastName(),
        );

        $profile = $this->profileCreator->create($profileCreateData);

        $clientCreateData = new ClientCreateData(
            $profile,
            $data->getEducation(),
            $data->getConsentPersonalData(),
        );

        $client = $this->clientCreator->create($clientCreateData);

        $this->entityManager->flush();

        return new ClientOutputData($client);
    }
}
