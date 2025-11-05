<?php

namespace App\Tests\Integration\Application\Client\ScoreCalculate;

use App\Application\Client\Connector\Command\ScoreCalculate\ClientEntityScoreCalculateCommand;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Infrastructure\Doctrine\Entity\Client;
use App\Tests\Support\TransactionalKernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ClientEntityScoreCalculateCommandTest extends TransactionalKernelTestCase
{
    protected EntityManagerInterface          $entityManager;
    private ClientEntityScoreCalculateCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();

        $this->command = $container->get(ClientEntityScoreCalculateCommand::class);
    }

    public function testExecuteCalculateScoreClientSuccessfully(): void
    {
        // user1@example.com - e-mail из фикстуры
        /** @var Client $client */
        $client       = $this->getClientByEmail(new ProfileEmail('user1@example.com'));
        $oldUpdatedAt = $client->getUpdatedAt();

        $outputData = $this->command->execute($client->getId());

        $this->assertSame(22, $outputData->getClientOutputData()->getScore());
        $this->assertTrue($oldUpdatedAt->getTimestamp() <= $outputData->getClientOutputData()->getUpdatedAt()->getTimestamp());
    }

    private function getClientByEmail(ProfileEmail $email): ?Client
    {
        return $this->entityManager->getRepository(Client::class)->createQueryBuilder('c')
            ->join('c.profile', 'p')
            ->andWhere('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
