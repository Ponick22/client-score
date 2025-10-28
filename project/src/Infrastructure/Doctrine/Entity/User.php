<?php

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Enum\UserRoleEnum;
use App\Domain\User\Exception\UserEmailInvalidException;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserHashPassword;
use App\Domain\User\ValueObject\UserRoleEnumCollection;
use App\Infrastructure\Doctrine\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserEntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $email;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

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

    /**
     * @throws UserEmailInvalidException
     */
    public function getEmail(): UserEmail
    {
        return new UserEmail($this->email);
    }

    public function setEmail(UserEmail $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?UserHashPassword
    {
        return $this->password ? UserHashPassword::fromHashPassword($this->password) : null;
    }

    public function setPassword(?UserHashPassword $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): UserRoleEnumCollection
    {
        $roles = array_map(
            fn(string $role) => UserRoleEnum::from($role),
            $this->roles
        );

        return new UserRoleEnumCollection($roles);
    }

    public function setRoles(UserRoleEnumCollection $roles): static
    {
        $this->roles = $roles->toArray();

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
