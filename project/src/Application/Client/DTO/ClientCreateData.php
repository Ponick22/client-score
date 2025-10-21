<?php

namespace App\Application\Client\DTO;

use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Profile\Entity\ProfileEntityInterface;

readonly class ClientCreateData
{
    public function __construct(
        protected ProfileEntityInterface $profile,
        protected EducationEnum          $education,
        protected bool                   $consentPersonalData,
    ) {}

    public function getProfile(): ProfileEntityInterface
    {
        return $this->profile;
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
