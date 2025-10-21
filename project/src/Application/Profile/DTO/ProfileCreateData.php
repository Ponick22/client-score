<?php

namespace App\Application\Profile\DTO;

use App\Domain\User\Entity\UserEntityInterface;

readonly class ProfileCreateData
{
    public function __construct(
        protected UserEntityInterface $user,
        protected string              $email,
        protected string              $phone,
        protected ?string             $firstName = null,
        protected ?string             $lastName = null,
    ) {}

    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
