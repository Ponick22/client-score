<?php

namespace App\Domain\Client\Factory;

use App\Domain\Client\Entity\ClientEntityInterface;

interface ClientEntityFactoryInterface
{
    public function create(): ClientEntityInterface;
}
