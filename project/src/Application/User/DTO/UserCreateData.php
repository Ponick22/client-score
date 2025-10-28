<?php

namespace App\Application\User\DTO;

use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Domain\User\ValueObject\UserRoleEnumCollection;

readonly class UserCreateData
{
    public function __construct(
        protected UserEmail              $email,
        protected ?UserPassword          $password = null,
        protected UserRoleEnumCollection $roles = new UserRoleEnumCollection(),
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
