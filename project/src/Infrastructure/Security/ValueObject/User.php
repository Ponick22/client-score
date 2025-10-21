<?php

namespace App\Infrastructure\Security\ValueObject;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private string  $identifier,
        private ?string $password = null,
        private array   $roles = [],
    ) {}

    public function getRoles(): array
    {
        $roles = array_map(fn($role) => str_starts_with(strtoupper($role), 'ROLE_') ? strtoupper($role) : 'ROLE_' . strtoupper($role), $this->roles);

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array)$this;

        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }
}
