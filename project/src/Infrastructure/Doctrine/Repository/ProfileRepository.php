<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Repository\ProfileRepositoryInterface;
use App\Domain\User\Entity\UserEntityInterface;
use App\Infrastructure\Doctrine\Entity\Profile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profile>
 */
class ProfileRepository extends ServiceEntityRepository implements ProfileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    public function getOne(int $id): ?ProfileEntityInterface
    {
        return $this->find($id);
    }

    public function getOneByUser(UserEntityInterface $user): ?ProfileEntityInterface
    {
        return $this->findOneBy(['user' => $user]);
    }

    public function getOneByEmail(string $email): ?ProfileEntityInterface
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function getOneByPhone(string $phone): ?ProfileEntityInterface
    {
        return $this->findOneBy(['phone' => $phone]);
    }
}
