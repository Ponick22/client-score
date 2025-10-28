<?php

namespace App\Presentation\EntryPoint\Data\Client\DTO;

use App\Application\Client\Connector\Command\EntityCreation\Contract\ClientEntityCreationDataInterface;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\ValueObject\UserEmail;

readonly class ClientEntityCreationData implements ClientEntityCreationDataInterface
{
    public function __construct(
        private UserEmail         $userEmail,
        private ProfileEmail      $profileEmail,
        private ProfilePhone      $phone,
        private ?ProfileFirstName $firstName,
        private ?ProfileLastName  $lastName,
        private EducationEnum     $education,
        private bool              $consentPersonalData,
    ) {}

    public function getUserEmail(): UserEmail
    {
        return $this->userEmail;
    }

    public function getProfileEmail(): ProfileEmail
    {
        return $this->profileEmail;
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
