<?php

namespace App\Presentation\EntryPoint\Command\User;

use App\Application\Exception\ValidationException;
use App\Application\User\Connector\Command\EntityCreation\UserEntityCreationCommand;
use App\Application\User\Model\UserRole;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Presentation\EntryPoint\Data\User\DTO\UserEntityCreationData;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name       : 'app:create-admin-user',
    description: 'Creates an admin user by email and password',
)]
readonly class CreateAdminUserCommand
{
    public function __construct(
        private UserEntityCreationCommand $userEntityCreationCommand,
    ) {}

    public function __invoke(
        #[Argument('The email of the admin.')] string    $email,
        #[Argument('The password of the admin.')] string $password,
        OutputInterface                                  $output
    ): int
    {
        try {
            $creationData = new UserEntityCreationData();
            $creationData
                ->setEmail($email)
                ->setPassword($password)
                ->setRoles([UserRole::ADMIN]);

            $this->userEntityCreationCommand->execute($creationData);
        } catch (ValidationException $e) {
            $output->writeln([
                '<error>Admin user creation failed: ' . $e->getMessage() . '</error>',
                '============',
                '',
            ]);

            foreach ($e->getErrors() as $error) {
                $output->writeln((string)$error);
            }

            $output->writeln([
                '',
                '============',
            ]);

            return Command::FAILURE;

        } catch (EntityManagerException $e) {
            $output->writeln(
                '<error>Admin user creation failed: ' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Admin user created</info>');

        return Command::SUCCESS;
    }
}
