<?php

namespace App\Domain\Profile\Factory;

use App\Domain\Profile\Entity\ProfileEntityInterface;

interface ProfileEntityFactoryInterface
{
    public function create(): ProfileEntityInterface;
}
