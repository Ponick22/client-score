<?php

namespace App\Domain\Client\Repository;

use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Repository\DTO\ClientFilterDataInterface;
use App\Domain\Client\ValueObject\ClientEntityCollectionAbstract;
use App\Domain\Profile\Entity\ProfileEntityInterface;

interface ClientRepositoryInterface
{
    public function getOne(int $id): ?ClientEntityInterface;
    public function getOneByProfile(ProfileEntityInterface $profile): ?ClientEntityInterface;

    public function getListByFilter(?ClientFilterDataInterface $filter = null): ClientEntityCollectionAbstract;

    public function getCountByFilter(?ClientFilterDataInterface $filter = null): int;
}
