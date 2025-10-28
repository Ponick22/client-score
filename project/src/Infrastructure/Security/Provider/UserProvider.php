<?php

namespace App\Infrastructure\Security\Provider;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserEmail;
use App\Infrastructure\Security\Factory\UserFactory;
use App\Infrastructure\Security\ValueObject\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserFactory             $userFactory,
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->getOneByEmail(new UserEmail($identifier));

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $this->userFactory->create($user);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }

        $identifier = $user->getUserIdentifier();

        return $this->loadUserByIdentifier($identifier);
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}
