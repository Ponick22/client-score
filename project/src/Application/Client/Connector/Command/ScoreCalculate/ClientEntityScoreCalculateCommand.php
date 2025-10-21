<?php

namespace App\Application\Client\Connector\Command\ScoreCalculate;

use App\Application\Client\ClientEntityGetter;
use App\Application\Client\Connector\Command\ScoreCalculate\DTO\OutputData;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;

readonly class ClientEntityScoreCalculateCommand
{
    public function __construct(
        private ClientEntityGetter     $getter,
        private ClientScoreCalculating $scoreCalculating,
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @throws ClientEntityNotFoundException
     * @throws EntityManagerException
     */
    public function execute(int $id): OutputData
    {
        $client = $this->getter->get($id);

        $scoringData = $this->scoreCalculating->calculate($client);

        $client->setScore($scoringData->getScore());

        $this->entityManager->flush();

        return new OutputData(
            new ClientOutputData($client),
            $scoringData
        );
    }
}
