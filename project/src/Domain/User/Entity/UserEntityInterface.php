<?php

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserHashPassword;
use App\Domain\User\ValueObject\UserRoleEnumCollection;

interface UserEntityInterface
{
    public function getId(): ?int;

    public function getEmail(): UserEmail;
    public function setEmail(UserEmail $email): self;

    public function getPassword(): ?UserHashPassword;
    public function setPassword(?UserHashPassword $password): self;

    public function getRoles(): UserRoleEnumCollection;
    public function setRoles(UserRoleEnumCollection $roles): self;

    public function getCreatedAt(): \DateTimeImmutable;

    public function getUpdatedAt(): \DateTimeImmutable;
}
