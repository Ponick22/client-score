<?php

namespace App\Application\Client\Connector\Command\EntityCreation\Contract;

use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\ValueObject\UserEmail;

interface ClientEntityCreationDataInterface
{
    // user data
    public function getUserEmail(): UserEmail;

    // profile data
    public function getProfileEmail(): ProfileEmail;
    public function getPhone(): ProfilePhone;
    public function getFirstName(): ?ProfileFirstName;
    public function getLastName(): ?ProfileLastName;

    // client data
    public function getEducation(): EducationEnum;
    public function getConsentPersonalData(): bool;
}
