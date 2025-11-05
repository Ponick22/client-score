<?php

namespace App\Tests\Integration\Infrastructure\Docrtine\Repository;

use App\Domain\User\ValueObject\UserEmail;
use App\Infrastructure\Doctrine\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->repository = $container->get(UserRepository::class);
    }

    public function testGetOneAndGetOneByEmail(): void
    {
        // user1@example.com - e-mail из фикстуры
        $email       = new UserEmail('user1@example.com');
        $userByEmail = $this->repository->getOneByEmail($email);

        self::assertNotNull($userByEmail);
        self::assertSame((string)$userByEmail->getEmail(), (string)$email);

        $userById = $this->repository->getOne($userByEmail->getId());

        self::assertNotNull($userById);
        self::assertSame($userByEmail->getId(), $userById->getId());

        self::assertNull($this->repository->getOne(-1));
        self::assertNull($this->repository->getOneByEmail(new UserEmail('user10@example.com')));
    }
}
