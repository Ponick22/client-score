<?php

namespace App\Application\User\Service;

use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Service\PasswordHasherInterface;

readonly class UserPasswordChanger
{
    public function __construct(
        private PasswordHasherInterface $hasher,
    ) {}

    public function change(UserEntityInterface $entity, string $password): void
    {
        $entity->changePassword($this->hasher->hash($entity, $password));
    }
}
