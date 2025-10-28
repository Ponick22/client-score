<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Exception\ProfileEmailInvalidException;
use App\Domain\Profile\Exception\ProfileFirstNameInvalidException;
use App\Domain\Profile\Exception\ProfileLastNameInvalidException;
use App\Domain\Profile\Exception\ProfilePhoneInvalidException;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\Entity\UserEntityInterface;
use App\Infrastructure\Doctrine\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Profile implements ProfileEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 255, unique: true)]
    private string $email;

    #[ORM\Column(length: 12, unique: true)]
    private string $phone;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

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

    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    public function setUser(UserEntityInterface $user): static
    {
        if (!$user instanceof User) {
            throw new \LogicException(sprintf('Expected %s', User::class));
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @throws ProfileEmailInvalidException
     */
    public function getEmail(): ProfileEmail
    {
        return new ProfileEmail($this->email);
    }

    public function setEmail(ProfileEmail $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @throws ProfilePhoneInvalidException
     */
    public function getPhone(): ProfilePhone
    {
        return new ProfilePhone($this->phone);
    }

    public function setPhone(ProfilePhone $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @throws ProfileFirstNameInvalidException
     */
    public function getFirstName(): ?ProfileFirstName
    {
        return $this->firstName ? new ProfileFirstName($this->firstName) : null;
    }

    public function setFirstName(?ProfileFirstName $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @throws ProfileLastNameInvalidException
     */
    public function getLastName(): ?ProfileLastName
    {
        return $this->lastName ? new ProfileLastName($this->lastName) : null;
    }

    public function setLastName(?ProfileLastName $lastName): static
    {
        $this->lastName = $lastName;

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
