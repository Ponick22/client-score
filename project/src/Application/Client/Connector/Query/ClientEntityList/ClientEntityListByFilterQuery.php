<?php

namespace App\Application\Client\Connector\Query\ClientEntityList;

use App\Application\Client\Connector\Query\ClientEntityList\DTO\ClientEntityListByFilterInterface;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\ValueObject\ClientOutputDataCollection;
use App\Domain\Client\Repository\ClientRepositoryInterface;

readonly class ClientEntityListByFilterQuery
{
    public function __construct(
        private ClientRepositoryInterface $repository
    ) {}

    public function execute(?ClientEntityListByFilterInterface $filter = null): ClientOutputDataCollection
    {
        $entities = $this->repository->getListByFilter($filter);

        $collection = new ClientOutputDataCollection();
        foreach ($entities as $entity) {
            $collection->add(new ClientOutputData($entity, $entity->getProfile()));
        }

        return $collection;
    }
}
