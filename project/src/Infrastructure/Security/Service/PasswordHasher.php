<?php

namespace App\Infrastructure\Security\Service;

use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Infrastructure\Security\Factory\UserFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class PasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private UserFactory                 $factory,
    ) {}

    public function hash(UserEntityInterface $user, string $password): string
    {
        $user = $this->factory->create($user);

        return $this->hasher->hashPassword($user, $password);
    }

    public function verify(UserEntityInterface $user, string $password): bool
    {
        $user = $this->factory->create($user);

        return $this->hasher->isPasswordValid($user, $password);
    }
}
