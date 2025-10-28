<?php

namespace App\Domain\Profile\Entity;

use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\Entity\UserEntityInterface;

interface ProfileEntityInterface
{
    public function getId(): ?int;

    public function getUser(): UserEntityInterface;
    public function setUser(UserEntityInterface $user): self;

    public function getEmail(): ProfileEmail;
    public function setEmail(ProfileEmail $email): self;

    public function getPhone(): ProfilePhone;
    public function setPhone(ProfilePhone $phone): self;

    public function getFirstName(): ?ProfileFirstName;
    public function setFirstName(?ProfileFirstName $firstName): self;

    public function getLastName(): ?ProfileLastName;
    public function setLastName(?ProfileLastName $lastName): self;

    public function getCreatedAt(): \DateTimeImmutable;

    public function getUpdatedAt(): \DateTimeImmutable;
}
