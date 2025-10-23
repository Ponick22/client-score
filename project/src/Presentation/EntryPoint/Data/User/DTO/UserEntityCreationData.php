<?php

namespace App\Presentation\EntryPoint\Data\User\DTO;

use App\Application\User\Connector\Command\EntityCreation\Contract\UserEntityCreationDataInterface;

class UserEntityCreationData implements UserEntityCreationDataInterface
{
    private string  $email;
    private ?string $password = null;
    private array   $roles    = [];

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
