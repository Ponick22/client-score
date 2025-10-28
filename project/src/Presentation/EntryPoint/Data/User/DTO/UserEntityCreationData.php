<?php

namespace App\Presentation\EntryPoint\Data\User\DTO;

use App\Application\User\Connector\Command\EntityCreation\Contract\UserEntityCreationDataInterface;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Domain\User\ValueObject\UserRoleEnumCollection;

readonly class UserEntityCreationData implements UserEntityCreationDataInterface
{
    public function __construct(
        private UserEmail              $email,
        private ?UserPassword          $password = null,
        private UserRoleEnumCollection $roles = new UserRoleEnumCollection(),
    ) {}

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function getPassword(): ?UserPassword
    {
        return $this->password;
    }

    public function getRoles(): UserRoleEnumCollection
    {
        return $this->roles;
    }
}
