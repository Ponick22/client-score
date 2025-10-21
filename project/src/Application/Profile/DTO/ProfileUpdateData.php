<?php

namespace App\Application\Profile\DTO;

readonly class ProfileUpdateData
{
    public function __construct(
        protected string  $email,
        protected string  $phone,
        protected ?string $firstName = null,
        protected ?string $lastName = null,
    ) {}

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
