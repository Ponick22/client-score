<?php

namespace App\Presentation\EntryPoint\Data\Client\DTO;

use App\Application\Client\Connector\Command\EntityUpdate\Contract\ClientEntityUpdateDataInterface;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;

readonly class ClientEntityUpdateData implements ClientEntityUpdateDataInterface
{
    public function __construct(
        private int               $id,
        private ProfileEmail      $email,
        private ProfilePhone      $phone,
        private ?ProfileFirstName $firstName,
        private ?ProfileLastName  $lastName,
        private EducationEnum     $education,
        private bool              $consentPersonalData,
    ) {}

    public function getId(): int
    {
        return $this->id;
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

    public function getEducation(): EducationEnum
    {
        return $this->education;
    }

    public function getConsentPersonalData(): bool
    {
        return $this->consentPersonalData;
    }
}
