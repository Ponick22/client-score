<?php

namespace App\Application\Client\Connector\Command\EntityUpdate\Contract;

use App\Domain\Client\Enum\EducationEnum;

interface ClientEntityUpdateDataInterface
{
    public function getId(): int;

    // user data
    public function getEmail(): string;

    // profile data
    public function getPhone(): string;
    public function getFirstName(): string;
    public function getLastName(): string;

    // client data
    public function getEducation(): EducationEnum;
    public function getConsentPersonalData(): bool;
}
