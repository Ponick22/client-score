<?php

namespace App\Tests\Unit\Application\Client\Connector\Command\ScoreCalculate;

use App\Application\Client\ClientEntityGetter;
use App\Application\Client\Connector\Command\ScoreCalculate\ClientEntityScoreCalculateCommand;
use App\Application\Client\DTO\ClientScoringData;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Application\Scoring\ValueObject\ScoringDataCollection;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Tests\Traits\ClientMocksTrait;
use PHPUnit\Framework\TestCase;

class ClientEntityScoreCalculateCommandTest extends TestCase
{
    use ClientMocksTrait;

    private ClientEntityGetter     $getterMock;
    private ClientScoreCalculating $scoreCalculatingMock;
    private EntityManagerInterface $entityManagerMock;

    private ClientEntityScoreCalculateCommand $command;

    protected function setUp(): void
    {
        $this->getterMock           = $this->createMock(ClientEntityGetter::class);
        $this->scoreCalculatingMock = $this->createMock(ClientScoreCalculating::class);
        $this->entityManagerMock    = $this->createMock(EntityManagerInterface::class);

        $this->command = new ClientEntityScoreCalculateCommand(
            $this->getterMock,
            $this->scoreCalculatingMock,
            $this->entityManagerMock
        );
    }

    public function testExecuteCalculatesAndPersistsScoreAndReturnsOutput(): void
    {
        $clientId = 1;

        $clientMock = $this->createClientMockComplete($this, $clientId);

        $this->getterMock->expects($this->once())
            ->method('get')
            ->with($clientId)
            ->willReturn($clientMock);

        $scoringData       = $this->createStub(ScoringDataCollection::class);
        $clientScoringData = new ClientScoringData(100, $scoringData);

        $this->scoreCalculatingMock->expects($this->once())
            ->method('calculate')
            ->with($clientMock)
            ->willReturn($clientScoringData);

        $clientMock->expects($this->once())->method('setScore')->with($clientScoringData->getScore());

        $this->entityManagerMock->expects($this->once())->method('flush');

        $result = $this->command->execute($clientId);

        self::assertSame($clientScoringData, $result->getClientScoringData());
        self::assertSame($clientId, $result->getClientOutputData()->getId());
    }
}
