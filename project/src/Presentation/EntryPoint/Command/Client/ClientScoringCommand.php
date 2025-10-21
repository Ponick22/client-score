<?php

namespace App\Presentation\EntryPoint\Command\Client;

use App\Application\Client\Connector\Command\ScoreCalculate\ClientEntityListScoreCalculateCommand;
use App\Application\Client\Connector\Command\ScoreCalculate\ClientEntityScoreCalculateCommand;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Domain\EntityManager\Exception\EntityManagerException;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name       : 'app:client-scoring',
    description: 'Calculate client scoring',
)]
readonly class ClientScoringCommand
{
    public function __construct(
        private ClientEntityScoreCalculateCommand     $scoreCalculateCommand,
        private ClientEntityListScoreCalculateCommand $listScoreCalculateCommand
    ) {}


    public function __invoke(
        OutputInterface                $output,
        #[Argument('Client ID.')] ?int $id = null,
    ): int
    {
        try {
            $table = new Table($output);
            $rows  = [];

            if ($id) {
                $result = $this->scoreCalculateCommand->execute($id);

                $scoringData = $result->getClientScoringData();

                $output->writeln("<info>Client ID: $id</info>");

                $table->setHeaderTitle('Details');
                $table->setHeaders(['Name', 'Value', 'Score']);
                foreach ($scoringData->getScoreDetails() as $detail) {
                    $rows[] = [
                        $detail->getName(),
                        $detail->getValue(),
                        $detail->getScore(),
                    ];
                }

                $rows[] = new TableSeparator();
                $rows[] = [
                    new TableCell('Total Score', ['colspan' => 2]),
                    $scoringData->getScore(),
                ];
            } else {
                $clients = $this->listScoreCalculateCommand->execute();

                $table->setHeaders(['Client ID', 'Score']);
                foreach ($clients as $client) {
                    $rows[] = [
                        $client->getId(),
                        $client->getScore(),
                    ];
                }
            }

            $table->setRows($rows);
            $table->render();

        } catch (ClientEntityNotFoundException|EntityManagerException $e) {
            $output->writeln('<error>Calculate client scoring failed: </error>' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
