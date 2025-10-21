<?php

namespace App\Infrastructure\Doctrine\EntityFactory;

use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\Factory\ProfileEntityFactoryInterface;
use App\Infrastructure\Doctrine\Entity\Profile;

class ProfileEntityFactory implements ProfileEntityFactoryInterface
{
    public function create(): ProfileEntityInterface
    {
        return new Profile();
    }
}
