<?php

namespace App\Tests\Unit\Application\Client\Connector\Command\EntityCreation;

use App\Application\Client\ClientEntityCreator;
use App\Application\Client\Connector\Command\EntityCreation\ClientEntityCreationCommand;
use App\Application\Client\Connector\Command\EntityCreation\Contract\ClientEntityCreationDataInterface;
use App\Application\Client\Connector\Command\EntityCreation\Validator\ClientEntityCreationValidator;
use App\Application\Client\DTO\ClientCreateData;
use App\Application\Exception\ValidationException;
use App\Application\Profile\DTO\ProfileCreateData;
use App\Application\Profile\ProfileEntityCreator;
use App\Application\User\DTO\UserCreateData;
use App\Application\User\UserEntityCreator;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\User\Enum\UserRoleEnum;
use App\Tests\Traits\ClientMocksTrait;
use App\Tests\Traits\ProfileDataTrait;
use App\Tests\Traits\UserDataTrait;
use App\Util\Validation\ValidationErrorCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ClientEntityCreationCommandTest extends TestCase
{
    use UserDataTrait;
    use ProfileDataTrait;
    use ClientMocksTrait;

    private ClientEntityCreationValidator $validatorMock;
    private UserEntityCreator             $userCreatorMock;
    private ProfileEntityCreator          $profileCreatorMock;
    private ClientEntityCreator           $clientCreatorMock;
    private EntityManagerInterface        $entityManagerMock;
    private ClientEntityCreationCommand   $command;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->validatorMock      = $this->createMock(ClientEntityCreationValidator::class);
        $this->userCreatorMock    = $this->createMock(UserEntityCreator::class);
        $this->profileCreatorMock = $this->createMock(ProfileEntityCreator::class);
        $this->clientCreatorMock  = $this->createMock(ClientEntityCreator::class);
        $this->entityManagerMock  = $this->createMock(EntityManagerInterface::class);

        $this->command = new ClientEntityCreationCommand(
            $this->validatorMock,
            $this->userCreatorMock,
            $this->profileCreatorMock,
            $this->clientCreatorMock,
            $this->entityManagerMock
        );

        $this->initUserData();
        $this->initProfileData();
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws ValidationException
     * @throws PhoneOperatorException
     */
    public function testExecuteCreatesClientSuccessfully(): void
    {
        $education = EducationEnum::Special;
        $consent   = false;

        $inputData = $this->createStub(ClientEntityCreationDataInterface::class);
        $inputData->method('getUserEmail')->willReturn($this->userEmail);
        $inputData->method('getProfileEmail')->willReturn($this->profileEmail);
        $inputData->method('getPhone')->willReturn($this->phone);
        $inputData->method('getFirstName')->willReturn($this->firstName);
        $inputData->method('getLastName')->willReturn($this->lastName);
        $inputData->method('getEducation')->willReturn($education);
        $inputData->method('getConsentPersonalData')->willReturn($consent);

        $validationErrors = $this->createStub(ValidationErrorCollection::class);
        $validationErrors->method('isValid')->willReturn(true);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($inputData)
            ->willReturn($validationErrors);

        $userId   = 1;
        $userMock = $this->createUserMock($this, $userId);

        $this->userCreatorMock->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($arg) use ($inputData) {
                return $arg instanceof UserCreateData
                    && $arg->getEmail() === $inputData->getUserEmail()
                    && $arg->getRoles()->toArray() === [UserRoleEnum::Client->value];
            }))
            ->willReturn($userMock);

        $profileId   = 2;
        $profileMock = $this->createProfileMock($this, $profileId, $userMock);
        $profileMock->method('getEmail')->willReturn($this->profileEmail);
        $profileMock->method('getPhone')->willReturn($this->phone);
        $profileMock->method('getFirstName')->willReturn($this->firstName);
        $profileMock->method('getLastName')->willReturn($this->lastName);

        $this->profileCreatorMock->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($arg) use ($inputData, $userMock) {
                return $arg instanceof ProfileCreateData
                    && $arg->getUser() === $userMock
                    && $arg->getEmail() === $inputData->getProfileEmail()
                    && $arg->getPhone() === $inputData->getPhone()
                    && $arg->getFirstName() === $inputData->getFirstName()
                    && $arg->getLastName() === $inputData->getLastName();
            }))
            ->willReturn($profileMock);

        $clientId   = 3;
        $clientMock = $this->createClientMock($this, $clientId, $profileMock);
        $clientMock->method('getEducation')->willReturn($education);
        $clientMock->method('getConsentPersonalData')->willReturn($consent);

        $this->clientCreatorMock->expects($this->once())
            ->method('create')
            ->with($this->callback(function ($arg) use ($inputData, $profileMock) {
                return $arg instanceof ClientCreateData
                    && $arg->getProfile() === $profileMock
                    && $arg->getEducation() === $inputData->getEducation()
                    && $arg->getConsentPersonalData() === $inputData->getConsentPersonalData();
            }))
            ->willReturn($clientMock);

        $this->entityManagerMock->expects($this->once())->method('flush');

        $result = $this->command->execute($inputData);

        $this->assertSame($inputData->getEducation(), $result->getEducation());
        $this->assertSame($inputData->getConsentPersonalData(), $result->getConsentPersonalData());
        $this->assertSame((string)$inputData->getProfileEmail(), $result->getProfile()->getEmail());
        $this->assertSame((string)$inputData->getPhone(), $result->getProfile()->getPhone());
        $this->assertSame((string)$inputData->getFirstName(), $result->getProfile()->getFirstName());
        $this->assertSame((string)$inputData->getLastName(), $result->getProfile()->getLastName());
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     */
    public function testExecuteThrowsValidationExceptionOnInvalidData(): void
    {
        $inputData = $this->createStub(ClientEntityCreationDataInterface::class);

        $validationErrors = $this->createStub(ValidationErrorCollection::class);
        $validationErrors->method('isValid')->willReturn(false);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($inputData)
            ->willReturn($validationErrors);

        $this->userCreatorMock->expects($this->never())->method('create');
        $this->profileCreatorMock->expects($this->never())->method('create');
        $this->clientCreatorMock->expects($this->never())->method('create');
        $this->entityManagerMock->expects($this->never())->method('flush');

        $this->expectException(ValidationException::class);

        $this->command->execute($inputData);
    }
}
