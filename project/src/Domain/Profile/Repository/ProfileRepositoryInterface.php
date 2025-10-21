<?php

namespace App\Domain\Profile\Repository;

use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\User\Entity\UserEntityInterface;

interface ProfileRepositoryInterface
{
    public function getOne(int $id): ?ProfileEntityInterface;
    public function getOneByUser(UserEntityInterface $user): ?ProfileEntityInterface;
    public function getOneByEmail(string $email): ?ProfileEntityInterface;
    public function getOneByPhone(string $phone): ?ProfileEntityInterface;
}
