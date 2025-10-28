<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\ValueObject\UserHashPassword;
use App\Domain\User\ValueObject\UserPassword;

interface PasswordHasherInterface
{
    public function hash(UserEntityInterface $user, UserPassword $password): UserHashPassword;

    public function verify(UserEntityInterface $user, UserPassword $password): bool;
}
