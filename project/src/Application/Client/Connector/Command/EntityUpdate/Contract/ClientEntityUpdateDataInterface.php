<?php

namespace App\Application\Client\Connector\Command\EntityUpdate\Contract;

use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;

interface ClientEntityUpdateDataInterface
{
    public function getId(): int;

    // profile data
    public function getEmail(): ProfileEmail;
    public function getPhone(): ProfilePhone;
    public function getFirstName(): ?ProfileFirstName;
    public function getLastName(): ?ProfileLastName;

    // client data
    public function getEducation(): EducationEnum;
    public function getConsentPersonalData(): bool;
}
