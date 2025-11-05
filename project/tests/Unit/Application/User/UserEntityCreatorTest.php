<?php

namespace App\Tests\Unit\Application\User;

use App\Application\User\DTO\UserCreateData;
use App\Application\User\UserEntityCreator;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\User\Entity\UserEntityInterface;
use App\Domain\User\Factory\UserEntityFactoryInterface;
use App\Domain\User\Service\PasswordHasherInterface;
use App\Domain\User\ValueObject\UserHashPassword;
use App\Tests\Traits\UserDataTrait;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserEntityCreatorTest extends KernelTestCase
{
    use UserDataTrait;

    private UserEntityFactoryInterface $factoryMock;
    private PasswordHasherInterface    $hasherMock;
    private EntityManagerInterface     $entityManagerMock;
    private UserEntityCreator          $creator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->factoryMock       = $this->createMock(UserEntityFactoryInterface::class);
        $this->hasherMock        = $this->createMock(PasswordHasherInterface::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->creator = new UserEntityCreator(
            $this->factoryMock,
            $this->hasherMock,
            $this->entityManagerMock
        );

        $this->initUserData();
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     */
    public function testCreateUserWithPassword(): void
    {
        $createData = new UserCreateData(
            $this->userEmail,
            $this->password,
            $this->roles
        );

        $entityMock = $this->createMock(UserEntityInterface::class);
        $this->factoryMock->expects($this->once())->method('create')->willReturn($entityMock);

        $entityMock->expects($this->once())->method('setEmail')->with($this->userEmail)->willReturnSelf();
        $entityMock->expects($this->once())->method('setRoles')->with($this->roles)->willReturnSelf();

        $hashPassword = UserHashPassword::fromHashPassword(password_hash($this->password, PASSWORD_BCRYPT));

        $this->hasherMock->expects($this->once())
            ->method('hash')
            ->with($entityMock, $this->password)
            ->willReturn($hashPassword);

        $entityMock->expects($this->once())->method('setPassword')->with($hashPassword)->willReturnSelf();

        $this->entityManagerMock->expects($this->once())->method('persist')->with($entityMock);

        $result = $this->creator->create($createData);

        $this->assertSame($entityMock, $result);
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     */
    public function testCreateUserWithoutPassword(): void
    {
        $createData = new UserCreateData(
            $this->userEmail,
            null,
            $this->roles
        );

        $entityMock = $this->createMock(UserEntityInterface::class);
        $this->factoryMock->expects($this->once())->method('create')->willReturn($entityMock);

        $entityMock->expects($this->once())->method('setEmail')->with($this->userEmail)->willReturnSelf();
        $entityMock->expects($this->once())->method('setRoles')->with($this->roles)->willReturnSelf();

        $this->hasherMock->expects($this->never())->method('hash');

        $entityMock->expects($this->never())->method('setPassword');

        $this->entityManagerMock->expects($this->once())->method('persist')->with($entityMock);

        $result = $this->creator->create($createData);

        $this->assertSame($entityMock, $result);
    }
}
