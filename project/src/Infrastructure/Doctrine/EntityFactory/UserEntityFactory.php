<?php

namespace App\Infrastructure\Doctrine\EntityFactory;

use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Factory\UserEntityFactoryInterface;
use App\Infrastructure\Doctrine\Entity\User;

class UserEntityFactory implements UserEntityFactoryInterface
{
    public function create(): UserEntityInterface
    {
        return new User();
    }
}
