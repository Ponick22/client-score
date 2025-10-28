<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Repository\ProfileRepositoryInterface;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfilePhone;
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

    public function getOneByEmail(ProfileEmail $email): ?ProfileEntityInterface
    {
        return $this->findOneBy(['email' => (string)$email]);
    }

    public function getOneByPhone(ProfilePhone $phone): ?ProfileEntityInterface
    {
        return $this->findOneBy(['phone' => (string)$phone]);
    }
}
