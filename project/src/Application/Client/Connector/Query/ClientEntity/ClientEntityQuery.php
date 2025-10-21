<?php

namespace App\Application\Client\Connector\Query\ClientEntity;

use App\Application\Client\ClientEntityGetter;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\Exception\ClientEntityNotFoundException;

readonly class ClientEntityQuery
{
    public function __construct(
        private ClientEntityGetter $getter
    ) {}

    /**
     * @throws ClientEntityNotFoundException
     */
    public function execute(int $id, bool $withProfile = false): ClientOutputData
    {
        $entity = $this->getter->get($id);

        $profile = null;
        if ($withProfile) {
            $profile = $entity->getProfile();
        }

        return new ClientOutputData($entity, $profile);
    }

}
