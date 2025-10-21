<?php

namespace App\Infrastructure\Security\Factory;

use App\Domain\User\Entity\UserEntityInterface;
use App\Infrastructure\Security\ValueObject\User;

class UserFactory
{
    public function create(UserEntityInterface $entity): User
    {
        return new User(
            $entity->getEmail(),
            $entity->getPassword(),
            $entity->getRoles()
        );
    }
}
