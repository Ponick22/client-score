<?php

namespace App\Application\Client\Connector\Command\ScoreCalculate;

use App\Application\Client\DTO\ClientEntityListByFilterData;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Application\Client\ValueObject\ClientOutputDataCollection;
use App\Application\Lock\Exception\ProcessLockedException;
use App\Application\Lock\LockServiceInterface;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Infrastructure\Doctrine\Repository\ClientRepository;

readonly class ClientEntityListScoreCalculateCommand
{
    public function __construct(
        private LockServiceInterface   $lockService,
        private ClientRepository       $repository,
        private ClientScoreCalculating $scoreCalculating,
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @throws EntityManagerException
     * @throws ProcessLockedException
     */
    public function execute(int $batchSize = 50): ClientOutputDataCollection
    {
        $resource = 'score-calculate-list';
        if (!$this->lockService->acquire($resource)) {
            throw new ProcessLockedException('score.error.process_locked');
        }

        try {
            $offset = 0;
            $count  = $this->repository->getCountByFilter();

            $collection = new ClientOutputDataCollection();

            while ($offset < $count) {
                $data = new ClientEntityListByFilterData();
                $data
                    ->setOffset($offset)
                    ->setLimit($batchSize);

                $clients = $this->repository->getListByFilter($data);

                foreach ($clients as $client) {
                    $scoringData = $this->scoreCalculating->calculate($client);

                    $client->setScore($scoringData->getScore());

                    $collection->add(new ClientOutputData($client));
                }

                $this->entityManager->flush();
                $this->entityManager->clear();

                $offset += $batchSize;
            }

            return $collection;
        } finally {
            $this->lockService->release($resource);
        }
    }
}
