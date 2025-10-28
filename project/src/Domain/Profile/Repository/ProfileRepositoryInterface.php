<?php

namespace App\Domain\Profile\Repository;

use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\Entity\UserEntityInterface;

interface ProfileRepositoryInterface
{
    public function getOne(int $id): ?ProfileEntityInterface;
    public function getOneByUser(UserEntityInterface $user): ?ProfileEntityInterface;
    public function getOneByEmail(ProfileEmail $email): ?ProfileEntityInterface;
    public function getOneByPhone(ProfilePhone $phone): ?ProfileEntityInterface;
}
