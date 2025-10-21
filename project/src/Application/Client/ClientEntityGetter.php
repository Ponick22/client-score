<?php

namespace App\Application\Client;

use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;

readonly class ClientEntityGetter
{
    public function __construct(
        private ClientRepositoryInterface $repository,
    ) {}

    /**
     * @throws ClientEntityNotFoundException
     */
    public function get(int $id): ClientEntityInterface
    {
        $entity = $this->repository->getOne($id);
        if (!$entity) {
            throw new ClientEntityNotFoundException($id);
        }

        return $entity;
    }
}
