<?php

namespace App\Tests\Integration\Application\User\Connector\EntityCreation;

use App\Application\Exception\ValidationException;
use App\Application\User\Connector\Command\EntityCreation\Contract\UserEntityCreationDataInterface;
use App\Application\User\Connector\Command\EntityCreation\UserEntityCreationCommand;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\User\Enum\UserRoleEnum;
use App\Domain\User\Exception\UserEmailInvalidException;
use App\Domain\User\Exception\UserPasswordInvalidException;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\UserEmail;
use App\Domain\User\ValueObject\UserPassword;
use App\Domain\User\ValueObject\UserRoleEnumCollection;
use App\Infrastructure\Doctrine\Entity\User;
use App\Tests\Support\TransactionalKernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;

class UserEntityCreationCommandTest extends TransactionalKernelTestCase
{
    protected EntityManagerInterface  $entityManager;
    private UserEntityCreationCommand $command;
    private PasswordHasherInterface   $passwordHasher;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();

        $this->command        = $container->get(UserEntityCreationCommand::class);
        $this->passwordHasher = $container->get(PasswordHasherInterface::class);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     * @throws EntityManagerException
     * @throws UserEmailInvalidException
     * @throws UserPasswordInvalidException
     */
    #[DataProvider('createUserSuccessProvider')]
    public function testExecuteCreateUserSuccessfully(string $email, ?string $password, array $roles): void
    {
        $email    = new UserEmail($email);
        $password = $password ? new UserPassword($password) : null;
        $roles    = new UserRoleEnumCollection($roles);

        $createData = $this->createStub(UserEntityCreationDataInterface::class);
        $createData->method('getEmail')->willReturn($email);
        $createData->method('getPassword')->willReturn($password);
        $createData->method('getRoles')->willReturn($roles);

        $outputData = $this->command->execute($createData);
        $this->assertSame((string)$email, $outputData->getEmail());
        $this->assertSame($roles->toArray(), $outputData->getRoles());
        $this->assertSame($outputData->getCreatedAt()->getTimestamp(), $outputData->getUpdatedAt()->getTimestamp());

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => (string)$email]);
        $this->assertNotNull($user);

        if ($password) {
            $this->assertTrue($this->passwordHasher->verify($user, $password));
        }
    }

    public static function createUserSuccessProvider(): array
    {
        return [
            'full'                  => [
                'user10@example.com',
                'Password123',
                [UserRoleEnum::Client],
            ],
            'no password'           => [
                'user11@example.com',
                '',
                [UserRoleEnum::Admin],
            ],
            'no password and roles' => [
                'user12@example.com',
                null,
                [],
            ],
            'full 2'                => [
                'user13@example.com',
                'Password123!@#',
                [UserRoleEnum::Client, UserRoleEnum::Admin],
            ],
        ];
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     */
    public function testExecuteThrowsValidationExceptionWhenEmailAlreadyExists(): void
    {
        // user1@example.com - e-mail из фикстуры
        $email = new UserEmail('user1@example.com');

        $createData = $this->createStub(UserEntityCreationDataInterface::class);
        $createData->method('getEmail')->willReturn($email);

        try {
            $this->command->execute($createData);

            $this->fail('Expected ValidationException for duplicate email');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();

            $this->assertCount(1, $errors);
            $this->assertSame('email', $errors[0]->getProperty());
            $this->assertSame('error.user.email_already_exists', $errors[0]->getMessage());
        } finally {
            $count = $this->entityManager->getRepository(User::class)->count(['email' => $email]);
            $this->assertSame(1, $count);
        }
    }
}
