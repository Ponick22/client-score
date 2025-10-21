<?php

namespace App\Domain\Profile\Entity;

use App\Domain\User\Entity\UserEntityInterface;

interface ProfileEntityInterface
{
    public function getId(): ?int;

    public function getUser(): UserEntityInterface;
    public function setUser(UserEntityInterface $user): self;

    public function getEmail(): ?string;
    public function setEmail(?string $email): self;

    public function getPhone(): ?string;
    public function changePhone(?string $phone): self;

    public function getFirstName(): ?string;
    public function setFirstName(?string $firstName): self;

    public function getLastName(): ?string;
    public function setLastName(?string $lastName): self;

    public function getCreatedAt(): \DateTimeImmutable;

    public function getUpdatedAt(): \DateTimeImmutable;
}
