<?php

namespace App\Domain\User\Entity;

interface UserEntityInterface
{
    public function getId(): ?int;

    public function getEmail(): string;
    public function setEmail(string $email): self;

    public function getPassword(): ?string;
    public function changePassword(?string $password): self;

    public function getRoles(): array;
    public function setRoles(array $roles): self;

    public function getCreatedAt(): \DateTimeImmutable;

    public function getUpdatedAt(): \DateTimeImmutable;
}
