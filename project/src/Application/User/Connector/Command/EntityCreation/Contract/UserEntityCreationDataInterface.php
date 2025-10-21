<?php

namespace App\Application\User\Connector\Command\EntityCreation\Contract;

interface UserEntityCreationDataInterface
{
    public function getEmail(): string;
    public function getPassword(): ?string;
    public function getRoles(): array;
}
