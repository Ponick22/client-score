<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Doctrine\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getOne(int $id): ?User
    {
        return $this->find($id);
    }

    public function getOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }
}
