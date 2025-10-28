<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Infrastructure\Doctrine\Repository\ClientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Client implements ClientEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Profile $profile;

    #[ORM\Column(type: 'string', enumType: EducationEnum::class)]
    private EducationEnum $education;

    #[ORM\Column]
    private bool $consentPersonalData;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $phoneOperator = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now             = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ProfileEntityInterface
    {
        return $this->profile;
    }

    public function setProfile(ProfileEntityInterface $profile): static
    {
        if (!$profile instanceof Profile) {
            throw new \LogicException(sprintf('Expected %s', Profile::class));
        }

        $this->profile = $profile;

        return $this;
    }

    public function getEducation(): EducationEnum
    {
        return $this->education;
    }

    public function setEducation(EducationEnum $education): static
    {
        $this->education = $education;

        return $this;
    }

    public function getConsentPersonalData(): bool
    {
        return $this->consentPersonalData;
    }

    public function setConsentPersonalData(bool $consentPersonalData): static
    {
        $this->consentPersonalData = $consentPersonalData;

        return $this;
    }

    public function getPhoneOperator(): ?PhoneOperator
    {
        return $this->phoneOperator ? PhoneOperator::fromPhoneOperator($this->phoneOperator) : null;
    }

    public function setPhoneOperator(?PhoneOperator $phoneOperator): static
    {
        $this->phoneOperator = $phoneOperator;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
