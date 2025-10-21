<?php

namespace App\Domain\User\Factory;

use App\Domain\User\Entity\UserEntityInterface;

interface UserEntityFactoryInterface
{
    public function create(): UserEntityInterface;
}
