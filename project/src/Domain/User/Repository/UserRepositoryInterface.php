<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\UserEntityInterface;

interface UserRepositoryInterface
{
    public function getOne(int $id): ?UserEntityInterface;

    public function getOneByEmail(string $email): ?UserEntityInterface;
}
