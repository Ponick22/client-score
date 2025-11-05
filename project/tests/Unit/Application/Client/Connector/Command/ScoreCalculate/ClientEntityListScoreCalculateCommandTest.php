<?php

namespace App\Tests\Unit\Application\Client\Connector\Command\ScoreCalculate;

use App\Application\Client\Connector\Command\ScoreCalculate\ClientEntityListScoreCalculateCommand;
use App\Application\Client\DTO\ClientEntityListByFilterData;
use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\DTO\ClientScoringData;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Application\Lock\Exception\ProcessLockedException;
use App\Application\Lock\LockServiceInterface;
use App\Application\Scoring\ValueObject\ScoringDataCollection;
use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use App\Infrastructure\Doctrine\ValueObject\ClientEntityCollection;
use App\Tests\Traits\ClientMocksTrait;
use PHPUnit\Framework\TestCase;

class ClientEntityListScoreCalculateCommandTest extends TestCase
{
    use ClientMocksTrait;

    private LockServiceInterface      $lockServiceMock;
    private ClientRepositoryInterface $repositoryMock;
    private ClientScoreCalculating    $scoreCalculatingMock;
    private EntityManagerInterface    $entityManagerMock;

    private ClientEntityListScoreCalculateCommand $command;

    protected function setUp(): void
    {
        $this->lockServiceMock      = $this->createMock(LockServiceInterface::class);
        $this->repositoryMock       = $this->createMock(ClientRepositoryInterface::class);
        $this->scoreCalculatingMock = $this->createMock(ClientScoreCalculating::class);
        $this->entityManagerMock    = $this->createMock(EntityManagerInterface::class);

        $this->command = new ClientEntityListScoreCalculateCommand(
            $this->lockServiceMock,
            $this->repositoryMock,
            $this->scoreCalculatingMock,
            $this->entityManagerMock
        );
    }

    /**
     * @throws ProcessLockedException
     * @throws EntityManagerException
     */
    public function testCalculatesScoresInBatchesAndReleasesLock(): void
    {
        $this->lockServiceMock->expects($this->once())
            ->method('acquire')
            ->willReturn(true);

        $this->lockServiceMock->expects($this->once())
            ->method('release');

        $batchSize = 2;
        $count     = 5;

        $this->repositoryMock->expects($this->once())
            ->method('getCountByFilter')
            ->willReturn($count);

        $clients = [
            $this->createClient(1),
            $this->createClient(2),
            $this->createClient(3),
            $this->createClient(4),
            $this->createClient(5),
        ];

        $expectedOffsets = [0, 2, 4];

        $this->repositoryMock->expects($this->exactly(3))
            ->method('getListByFilterWithProfile')
            ->with($this->callback(function (ClientEntityListByFilterData $data) use (&$expectedOffsets, $batchSize) {
                if ($data->getLimit() !== $batchSize) {
                    return false;
                }

                $expected = array_shift($expectedOffsets);

                return $data->getOffset() === $expected;
            }))
            ->willReturnCallback(function (ClientEntityListByFilterData $data) use ($clients, $batchSize) {
                return new ClientEntityCollection(array_slice($clients, $data->getOffset(), $data->getLimit()));
            });

        $details = new ScoringDataCollection();

        $this->scoreCalculatingMock->expects($this->exactly($count))
            ->method('calculate')
            ->willReturnOnConsecutiveCalls(
                new ClientScoringData(10, $details),
                new ClientScoringData(20, $details),
                new ClientScoringData(30, $details),
                new ClientScoringData(40, $details),
                new ClientScoringData(50, $details),
            );

        $this->entityManagerMock->expects($this->exactly(3))->method('flush');
        $this->entityManagerMock->expects($this->exactly(3))->method('clear');

        $collection = $this->command->execute($batchSize);

        $scores = array_map(
            fn(ClientOutputData $item) => $item->getScore(),
            $collection->toArray()
        );

        self::assertCount($count, $collection);
        self::assertSame([10, 20, 30, 40, 50], $scores);
    }

    /**
     * @throws EntityManagerException
     */
    public function testThrowsWhenProcessLocked(): void
    {
        $this->lockServiceMock->expects($this->once())
            ->method('acquire')
            ->willReturn(false);

        $this->repositoryMock->expects($this->never())->method('getCountByFilter');
        $this->repositoryMock->expects($this->never())->method('getListByFilterWithProfile');
        $this->scoreCalculatingMock->expects($this->never())->method('calculate');
        $this->entityManagerMock->expects($this->never())->method('flush');
        $this->entityManagerMock->expects($this->never())->method('clear');

        $this->expectException(ProcessLockedException::class);

        $this->command->execute();
    }

    private function createClient(int $id): ClientEntityInterface
    {
        $now = new \DateTimeImmutable();

        $client = $this->createClientMock($this, $id);
        $client->method('getEducation')->willReturn(EducationEnum::Higher);
        $client->method('getConsentPersonalData')->willReturn(true);
        $client->method('getPhoneOperator')->willReturn(PhoneOperator::fromPhoneOperator('operator'));
        $client->method('getCreatedAt')->willReturn($now);
        $client->method('getUpdatedAt')->willReturn($now);

        $score = null;
        $client->method('setScore')
            ->willReturnCallback(function (int $s) use (&$score, $client) {
                $score = $s;
                return $client;
            });
        $client->method('getScore')
            ->willReturnCallback(function () use (&$score) {
                return $score;
            });

        return $client;
    }
}
