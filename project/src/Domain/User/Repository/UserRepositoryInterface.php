<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\ValueObject\UserEmail;

interface UserRepositoryInterface
{
    public function getOne(int $id): ?UserEntityInterface;

    public function getOneByEmail(UserEmail $email): ?UserEntityInterface;
}
