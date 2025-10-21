<?php

namespace App\Application\Client\Connector\Query\ClientEntityList;

use App\Application\Client\Connector\Query\ClientEntityList\DTO\ClientEntityListByFilterInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;

readonly class ClientEntityCountByFilterQuery
{
    public function __construct(
        private ClientRepositoryInterface $repository
    ) {}

    public function execute(?ClientEntityListByFilterInterface $filter = null): int
    {
        return $this->repository->getCountByFilter($filter);
    }
}
