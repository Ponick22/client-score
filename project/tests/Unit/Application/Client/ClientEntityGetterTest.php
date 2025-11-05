<?php

namespace App\Tests\Unit\Application\Client;

use App\Application\Client\ClientEntityGetter;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Repository\ClientRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientEntityGetterTest extends KernelTestCase
{
    private ClientRepositoryInterface $repositoryMock;
    private ClientEntityGetter        $getter;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ClientRepositoryInterface::class);

        $this->getter = new ClientEntityGetter($this->repositoryMock);
    }

    public function testGetReturnsEntity(): void
    {
        $clientId = 1;

        $entityMock = $this->createMock(ClientEntityInterface::class);

        $this->repositoryMock->expects($this->once())
            ->method('getOne')
            ->with($clientId)
            ->willReturn($entityMock);

        $result = $this->getter->get($clientId);

        $this->assertSame($entityMock, $result);
    }

    public function testGetThrowsExceptionWhenEntityNotFound(): void
    {
        $clientId = 100;

        $this->repositoryMock->expects($this->once())
            ->method('getOne')
            ->with($clientId)
            ->willReturn(null);

        $this->expectException(ClientEntityNotFoundException::class);

        $this->getter->get($clientId);
    }
}
