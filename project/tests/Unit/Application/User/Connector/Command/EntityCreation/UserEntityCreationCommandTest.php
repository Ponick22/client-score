<?php

namespace App\Tests\Unit\Application\User\Connector\Command\EntityCreation;

use App\Application\Exception\ValidationException;
use App\Application\User\Connector\Command\EntityCreation\Contract\UserEntityCreationDataInterface;
use App\Application\User\Connector\Command\EntityCreation\UserEntityCreationCommand;
use App\Application\User\Connector\Command\EntityCreation\Validator\UserEntityCreationValidator;
use App\Application\User\DTO\UserCreateData;
use App\Application\User\UserEntityCreator;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\User\Entity\UserEntityInterface;
use App\Tests\Traits\UserDataTrait;
use App\Util\Validation\ValidationErrorCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class UserEntityCreationCommandTest extends TestCase
{
    use UserDataTrait;

    private UserEntityCreationValidator $validatorMock;
    private UserEntityCreator           $creatorMock;
    private EntityManagerInterface      $entityManagerMock;
    private UserEntityCreationCommand   $command;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->validatorMock     = $this->createMock(UserEntityCreationValidator::class);
        $this->creatorMock       = $this->createMock(UserEntityCreator::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->command = new UserEntityCreationCommand(
            $this->validatorMock,
            $this->creatorMock,
            $this->entityManagerMock
        );

        $this->initUserData();
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws ValidationException
     */
    public function testExecuteCreatesUserSuccessfully(): void
    {
        $inputData = $this->createStub(UserEntityCreationDataInterface::class);
        $inputData->method('getEmail')->willReturn($this->userEmail);
        $inputData->method('getPassword')->willReturn($this->password);
        $inputData->method('getRoles')->willReturn($this->roles);

        $validationErrors = $this->createStub(ValidationErrorCollection::class);
        $validationErrors->method('isValid')->willReturn(true);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($inputData)
            ->willReturn($validationErrors);

        $userId     = 1;
        $entityMock = $this->createMock(UserEntityInterface::class);
        $entityMock->method('getId')->willReturn($userId);
        $entityMock->method('getEmail')->willReturn($this->userEmail);
        $entityMock->method('getRoles')->willReturn($this->roles);

        $this->creatorMock->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($arg) use ($inputData) {
                return $arg instanceof UserCreateData
                    && $arg->getEmail() === $inputData->getEmail()
                    && $arg->getPassword() === $inputData->getPassword()
                    && $arg->getRoles() === $inputData->getRoles();
            }))
            ->willReturn($entityMock);

        $this->entityManagerMock->expects($this->once())->method('flush');

        $result = $this->command->execute($inputData);

        $this->assertSame((string)$inputData->getEmail(), $result->getEmail());
        $this->assertSame($inputData->getRoles()->toArray(), $result->getRoles());
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     */
    public function testExecuteThrowsValidationExceptionOnInvalidData(): void
    {
        $inputData = $this->createStub(UserEntityCreationDataInterface::class);

        $validationErrors = $this->createStub(ValidationErrorCollection::class);
        $validationErrors->method('isValid')->willReturn(false);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($inputData)
            ->willReturn($validationErrors);

        $this->creatorMock->expects($this->never())->method('create');
        $this->entityManagerMock->expects($this->never())->method('flush');

        $this->expectException(ValidationException::class);

        $this->command->execute($inputData);
    }
}
