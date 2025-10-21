<?php

namespace App\Application\User\DTO;

readonly class UserCreateData
{
    public function __construct(
        protected string  $email,
        protected ?string $password = null,
        protected array   $roles = [],
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
