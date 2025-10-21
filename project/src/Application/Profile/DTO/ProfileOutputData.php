<?php

namespace App\Application\Profile\DTO;

use App\Domain\Profile\Entity\ProfileEntityInterface;

class ProfileOutputData
{
    protected int                $id;
    protected int                $userId;
    protected ?string            $email;
    protected ?string            $phone;
    protected ?string            $firstName;
    protected ?string            $lastName;
    protected \DateTimeImmutable $createdAt;
    protected \DateTimeImmutable $updatedAt;

    public function __construct(ProfileEntityInterface $entity)
    {
        $this->id        = $entity->getId();
        $this->userId    = $entity->getUser()->getId();
        $this->email     = $entity->getEmail();
        $this->phone     = $entity->getPhone();
        $this->firstName = $entity->getFirstName();
        $this->lastName  = $entity->getLastName();
        $this->createdAt = $entity->getCreatedAt();
        $this->updatedAt = $entity->getUpdatedAt();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
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
