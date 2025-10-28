<?php

namespace App\Application\Profile\DTO;

use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\Entity\UserEntityInterface;

readonly class ProfileCreateData
{
    public function __construct(
        protected UserEntityInterface $user,
        protected ProfileEmail        $email,
        protected ProfilePhone        $phone,
        protected ?ProfileFirstName   $firstName = null,
        protected ?ProfileLastName    $lastName = null,
    ) {}

    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    public function getEmail(): ProfileEmail
    {
        return $this->email;
    }

    public function getPhone(): ProfilePhone
    {
        return $this->phone;
    }

    public function getFirstName(): ?ProfileFirstName
    {
        return $this->firstName;
    }

    public function getLastName(): ?ProfileLastName
    {
        return $this->lastName;
    }
}
