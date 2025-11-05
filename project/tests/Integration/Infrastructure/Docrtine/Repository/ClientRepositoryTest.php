<?php

namespace App\Tests\Integration\Infrastructure\Docrtine\Repository;

use App\Domain\Client\Repository\DTO\ClientFilterDataInterface;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Infrastructure\Doctrine\Repository\ClientRepository;
use App\Infrastructure\Doctrine\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientRepositoryTest extends KernelTestCase
{
    private ClientRepository  $repository;
    private ProfileRepository $profileRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->repository        = $container->get(ClientRepository::class);
        $this->profileRepository = $container->get(ProfileRepository::class);
    }

    public function testGetOneAndGetOneByProfile(): void
    {
        // user1@example.com - e-mail из фикстуры
        $profileEmail   = new ProfileEmail('user1@example.com');
        $profileByEmail = $this->profileRepository->getOneByEmail($profileEmail);
        self::assertNotNull($profileByEmail);

        $clientByProfile = $this->repository->getOneByProfile($profileByEmail);
        self::assertNotNull($clientByProfile);
        self::assertSame($clientByProfile->getProfile(), $profileByEmail);

        $clientById = $this->repository->getOne($clientByProfile->getId());

        self::assertNotNull($clientById);
        self::assertSame($clientById->getId(), $clientByProfile->getId());

        self::assertNull($this->repository->getOne(-1));

        // admin2@example.com - e-mail из фикстуры
        $adminProfile = $this->profileRepository->getOneByEmail(new ProfileEmail('admin2@example.com'));
        self::assertNull($this->repository->getOneByProfile($adminProfile));
    }

    public function testGetListByFilterWithProfileAndCount(): void
    {
        $list  = $this->repository->getListByFilterWithProfile();
        $count = $this->repository->getCountByFilter();

        self::assertSame($list->count(), $count);
        self::assertNotNull($list[0]->getProfile());

        $filter1 = $this->createStub(ClientFilterDataInterface::class);
        $filter1->method('getOffset')->willReturn(0);
        $filter1->method('getLimit')->willReturn(1);

        $list1 = $this->repository->getListByFilterWithProfile($filter1);
        self::assertCount(1, $list1);

        $filter2 = $this->createStub(ClientFilterDataInterface::class);
        $filter2->method('getOffset')->willReturn(1);
        $filter2->method('getLimit')->willReturn(1);

        $list2 = $this->repository->getListByFilterWithProfile($filter2);
        self::assertCount(1, $list2);

        self::assertNotSame($list1->getFirst()->getId(), $list2->getFirst()->getId());

        $filter3 = $this->createStub(ClientFilterDataInterface::class);
        $filter3->method('getOffset')->willReturn(100);
        $filter3->method('getLimit')->willReturn(10);

        $list3 = $this->repository->getListByFilterWithProfile($filter3);
        self::assertCount(0, $list3);

        $filter4 = $this->createStub(ClientFilterDataInterface::class);
        $filter4->method('getOffset')->willReturn(-1);
        $filter4->method('getLimit')->willReturn(-1);

        $list4 = $this->repository->getListByFilterWithProfile($filter4);
        self::assertSame($list4->count(), $count);
    }
}
