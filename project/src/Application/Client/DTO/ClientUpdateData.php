<?php

namespace App\Application\Client\DTO;

use App\Domain\Client\Enum\EducationEnum;

readonly class ClientUpdateData
{
    public function __construct(
        protected EducationEnum $education,
        protected bool          $consentPersonalData,
        protected ?string       $profilePhone,
    ) {}

    public function getEducation(): EducationEnum
    {
        return $this->education;
    }

    public function getConsentPersonalData(): bool
    {
        return $this->consentPersonalData;
    }

    public function getProfilePhone(): ?string
    {
        return $this->profilePhone;
    }
}
