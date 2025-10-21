<?php

namespace App\Infrastructure\Doctrine\EntityFactory;

use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Factory\ClientEntityFactoryInterface;
use App\Infrastructure\Doctrine\Entity\Client;

class ClientEntityFactory implements ClientEntityFactoryInterface
{
    public function create(): ClientEntityInterface
    {
        return new Client();
    }
}
