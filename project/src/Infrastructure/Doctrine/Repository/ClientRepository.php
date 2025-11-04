<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\Client\Repository\DTO\ClientFilterDataInterface;
use App\Domain\Client\ValueObject\ClientEntityCollectionAbstract;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Infrastructure\Doctrine\Entity\Client;
use App\Infrastructure\Doctrine\ValueObject\ClientEntityCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getOne(int $id): ?ClientEntityInterface
    {
        return $this->find($id);
    }

    public function getOneByProfile(ProfileEntityInterface $profile): ?ClientEntityInterface
    {
        return $this->findOneBy(['profile' => $profile]);
    }

    public function getListByFilterWithProfile(?ClientFilterDataInterface $filter = null): ClientEntityCollectionAbstract
    {
        $entities = $this->createQueryBuilderByFilter($filter)
            ->leftJoin('c.profile', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getResult();

        return new ClientEntityCollection($entities);
    }

    public function getCountByFilter(?ClientFilterDataInterface $filter = null): int
    {
        return $this->createQueryBuilderByFilter($filter)
            ->select('COUNT(c.id) AS count')
            ->getQuery()
            ->getResult()[0]['count'] ?? 0;
    }

    private function createQueryBuilderByFilter(?ClientFilterDataInterface $filter = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('c');

        if ($filter and $filter->getOffset() !== null and $filter->getLimit() !== null) {
            $queryBuilder
                ->setFirstResult($filter->getOffset())
                ->setMaxResults($filter->getLimit());
        }

        return $queryBuilder;
    }
}
