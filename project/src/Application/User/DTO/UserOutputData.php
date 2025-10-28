<?php

namespace App\Application\User\DTO;

use App\Domain\User\Entity\UserEntityInterface;

class UserOutputData
{
    protected int                $id;
    protected string             $email;
    protected array              $roles;
    protected \DateTimeImmutable $createdAt;
    protected \DateTimeImmutable $updatedAt;

    public function __construct(UserEntityInterface $entity)
    {
        $this->id        = $entity->getId();
        $this->email     = $entity->getEmail();
        $this->roles     = $entity->getRoles()->toArray();
        $this->createdAt = $entity->getCreatedAt();
        $this->updatedAt = $entity->getUpdatedAt();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
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
