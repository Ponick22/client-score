<?php

namespace App\Application\User\Connector\Command\EntityCreation\Contract;

use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Domain\User\ValueObject\UserRoleEnumCollection;

interface UserEntityCreationDataInterface
{
    public function getEmail(): UserEmail;
    public function getPassword(): ?UserPassword;
    public function getRoles(): UserRoleEnumCollection;
}
