<?php

namespace App\Tests\Integration\Infrastructure\Docrtine\Repository;

use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\ValueObject\UserEmail;
use App\Infrastructure\Doctrine\Repository\ProfileRepository;
use App\Infrastructure\Doctrine\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfileRepositoryTest extends KernelTestCase
{
    private ProfileRepository $repository;
    private UserRepository    $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->repository     = $container->get(ProfileRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testGetOneAndGetOneByUser(): void
    {
        // user1@example.com - e-mail из фикстуры
        $userEmail   = new UserEmail('user1@example.com');
        $userByEmail = $this->userRepository->getOneByEmail($userEmail);
        self::assertNotNull($userByEmail);

        $profileByUser = $this->repository->getOneByUser($userByEmail);
        self::assertNotNull($profileByUser);
        self::assertSame($profileByUser->getUser(), $userByEmail);

        $profileById = $this->repository->getOne($profileByUser->getId());

        self::assertNotNull($profileById);
        self::assertSame($profileById->getId(), $profileByUser->getId());

        self::assertNull($this->repository->getOne(-1));

        // admin@example.com - e-mail из фикстуры
        $adminUser = $this->userRepository->getOneByEmail(new UserEmail('admin@example.com'));
        self::assertNull($this->repository->getOneByUser($adminUser));
    }

    public function testGetOneByEmail(): void
    {
        // user1@example.com - e-mail из фикстуры
        $profileEmail = new ProfileEmail('user1@example.com');

        $profileByEmail = $this->repository->getOneByEmail($profileEmail);
        self::assertNotNull($profileByEmail);
        self::assertSame((string)$profileByEmail->getEmail(), (string)$profileEmail);

        self::assertNull($this->repository->getOneByEmail(new ProfileEmail('user10@example.com')));
    }

    public function testGetOneByPhone(): void
    {
        $profilePhone = new ProfilePhone('+79123456789');

        $profileByPhone = $this->repository->getOneByPhone($profilePhone);
        self::assertNotNull($profileByPhone);
        self::assertSame((string)$profileByPhone->getPhone(), (string)$profilePhone);

        self::assertNull($this->repository->getOneByPhone(new ProfilePhone('+71111111111')));
    }
}
