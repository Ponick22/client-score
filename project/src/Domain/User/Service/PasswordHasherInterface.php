<?php

namespace App\Domain\User\Service;

use App\Domain\User\Entity\UserEntityInterface;

interface PasswordHasherInterface
{
    public function hash(UserEntityInterface $user, string $password): string;

    public function verify(UserEntityInterface $user, string $password): bool;
}
