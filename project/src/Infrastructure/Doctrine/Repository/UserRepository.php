<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserEmail;
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

    public function getOne(int $id): ?UserEntityInterface
    {
        return $this->find($id);
    }

    public function getOneByEmail(UserEmail $email): ?UserEntityInterface
    {
        return $this->findOneBy(['email' => (string)$email]);
    }
}
