<?php

namespace App\Tests\Integration\Application\Client\ScoreCalculate;

use App\Application\Client\Connector\Command\ScoreCalculate\ClientEntityListScoreCalculateCommand;
use App\Tests\Support\TransactionalKernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ClientEntityListScoreCalculateCommandTest extends TransactionalKernelTestCase
{
    protected EntityManagerInterface              $entityManager;
    private ClientEntityListScoreCalculateCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();

        $this->command = $container->get(ClientEntityListScoreCalculateCommand::class);
    }

    public function testExecuteCalculateScoreClientListSuccessfully(): void
    {
        $outputData = $this->command->execute();

        // 9 клиентов в фикстуре
        $this->assertCount(9, $outputData);
        foreach ($outputData as $clientOutputData) {
            $this->assertSame(22, $clientOutputData->getScore());
        }
    }
}
