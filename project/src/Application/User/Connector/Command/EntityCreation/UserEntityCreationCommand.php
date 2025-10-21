<?php

namespace App\Application\User\Connector\Command\EntityCreation;

use App\Application\Exception\ValidationException;
use App\Application\User\Connector\Command\EntityCreation\Contract\UserEntityCreationDataInterface;
use App\Application\User\Connector\Command\EntityCreation\Validator\UserEntityCreationValidator;
use App\Application\User\DTO\UserCreateData;
use App\Application\User\DTO\UserOutputData;
use App\Application\User\UserEntityCreator;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;

readonly class UserEntityCreationCommand
{
    public function __construct(
        private UserEntityCreationValidator $validator,
        private UserEntityCreator           $creator,
        private EntityManagerInterface      $entityManager,
    ) {}

    /**
     * @throws ValidationException
     * @throws EntityManagerException
     */
    public function execute(UserEntityCreationDataInterface $data): UserOutputData
    {
        $errors = $this->validator->validate($data);

        if (!$errors->isValid()) {
            throw new ValidationException($errors);
        }

        $createData = new UserCreateData(
            $data->getEmail(),
            $data->getPassword(),
            $data->getRoles()
        );

        $entity = $this->creator->create($createData);

        $this->entityManager->flush();

        return new UserOutputData($entity);
    }
}
