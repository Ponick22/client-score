<?php

namespace App\Infrastructure\Security\Service;

use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\UserHashPassword;
use App\Domain\User\ValueObject\UserPassword;
use App\Infrastructure\Security\Factory\UserFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class PasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private UserFactory                 $factory,
    ) {}

    public function hash(UserEntityInterface $user, UserPassword $password): UserHashPassword
    {
        $user = $this->factory->create($user);

        return UserHashPassword::fromHashPassword($this->hasher->hashPassword($user, $password));
    }

    public function verify(UserEntityInterface $user, UserPassword $password): bool
    {
        $user = $this->factory->create($user);

        return $this->hasher->isPasswordValid($user, $password);
    }
}
