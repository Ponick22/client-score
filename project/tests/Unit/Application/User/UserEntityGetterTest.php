<?php

namespace App\Tests\Unit\Application\User;

use App\Application\User\Exception\UserEntityNotFoundException;
use App\Application\User\UserEntityGetter;
use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserEntityGetterTest extends KernelTestCase
{
    private UserRepositoryInterface $repositoryMock;
    private UserEntityGetter        $getter;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(UserRepositoryInterface::class);

        $this->getter = new UserEntityGetter($this->repositoryMock);
    }

    /**
     * @throws Exception
     * @throws UserEntityNotFoundException
     */
    public function testGetReturnsEntity(): void
    {
        $userId = 1;

        $entityMock = $this->createMock(UserEntityInterface::class);

        $this->repositoryMock->expects($this->once())
            ->method('getOne')
            ->with($userId)
            ->willReturn($entityMock);

        $result = $this->getter->get($userId);

        $this->assertSame($entityMock, $result);
    }

    public function testGetThrowsExceptionWhenEntityNotFound(): void
    {
        $userId = 100;

        $this->repositoryMock->expects($this->once())
            ->method('getOne')
            ->with($userId)
            ->willReturn(null);

        $this->expectException(UserEntityNotFoundException::class);

        $this->getter->get($userId);
    }
}
