<?php

namespace App\Application\Client\DTO;

use App\Application\Profile\DTO\ProfileOutputData;
use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Profile\Entity\ProfileEntityInterface;

class ClientOutputData
{
    protected int                $id;
    protected int                $profileId;
    protected ?ProfileOutputData $profile = null;
    protected EducationEnum      $education;
    protected bool               $consentPersonalData;
    protected ?string            $phoneOperator;
    protected ?int               $score;
    protected \DateTimeImmutable $createdAt;
    protected \DateTimeImmutable $updatedAt;

    public function __construct(
        ClientEntityInterface   $entity,
        ?ProfileEntityInterface $profile = null,
    )
    {
        $this->id                  = $entity->getId();
        $this->profileId           = $entity->getProfile()->getId();
        $this->education           = $entity->getEducation();
        $this->consentPersonalData = $entity->getConsentPersonalData();
        $this->phoneOperator       = $entity->getPhoneOperator();
        $this->score               = $entity->getScore();
        $this->createdAt           = $entity->getCreatedAt();
        $this->updatedAt           = $entity->getUpdatedAt();

        if ($profile) {
            $this->profile = new ProfileOutputData($profile);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProfileId(): int
    {
        return $this->profileId;
    }

    public function getProfile(): ?ProfileOutputData
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

    public function getPhoneOperator(): ?string
    {
        return $this->phoneOperator;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
