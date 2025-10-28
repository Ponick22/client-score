<?php

namespace App\Domain\Client\Entity;

use App\Domain\Client\Enum\EducationEnum;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use App\Domain\Profile\Entity\ProfileEntityInterface;

interface ClientEntityInterface
{
    public function getId(): ?int;

    public function getProfile(): ProfileEntityInterface;
    public function setProfile(ProfileEntityInterface $profile): self;

    public function getEducation(): EducationEnum;
    public function setEducation(EducationEnum $education): self;

    public function getConsentPersonalData(): bool;
    public function setConsentPersonalData(bool $consentPersonalData): self;

    public function getPhoneOperator(): ?PhoneOperator;
    public function setPhoneOperator(?PhoneOperator $phoneOperator): self;

    public function getScore(): ?int;
    public function setScore(?int $score): self;

    public function getCreatedAt(): \DateTimeImmutable;

    public function getUpdatedAt(): \DateTimeImmutable;
}
