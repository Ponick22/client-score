<?php

namespace App\Tests\Unit\Application\Client\Connector\Command\EntityUpdate;

use App\Application\Client\ClientEntityGetter;
use App\Application\Client\Connector\Command\EntityUpdate\ClientEntityUpdateCommand;
use App\Application\Client\Connector\Command\EntityUpdate\Contract\ClientEntityUpdateDataInterface;
use App\Application\Client\Connector\Command\EntityUpdate\Validator\ClientEntityUpdateValidator;
use App\Application\Client\DTO\ClientScoringData;
use App\Application\Client\Exception\ClientEntityNotFoundException;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Application\Exception\ValidationException;
use App\Application\Scoring\ValueObject\ScoringDataCollection;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\PhoneOperator\PhoneOperatorGetterInterface;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Tests\Traits\ClientMocksTrait;
use App\Tests\Traits\ProfileDataTrait;
use App\Util\Validation\ValidationErrorCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ClientEntityUpdateCommandTest extends TestCase
{
    use ProfileDataTrait;
    use ClientMocksTrait;

    private ClientEntityGetter           $clientEntityGetterMock;
    private ClientEntityUpdateValidator  $validatorMock;
    private PhoneOperatorGetterInterface $phoneOperatorGetterMock;
    private ClientScoreCalculating       $scoreCalculatingMock;
    private EntityManagerInterface       $entityManagerMock;
    private ClientEntityUpdateCommand    $command;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->clientEntityGetterMock  = $this->createMock(ClientEntityGetter::class);
        $this->validatorMock           = $this->createMock(ClientEntityUpdateValidator::class);
        $this->phoneOperatorGetterMock = $this->createMock(PhoneOperatorGetterInterface::class);
        $this->scoreCalculatingMock    = $this->createMock(ClientScoreCalculating::class);
        $this->entityManagerMock       = $this->createMock(EntityManagerInterface::class);

        $this->command = new ClientEntityUpdateCommand(
            $this->clientEntityGetterMock,
            $this->validatorMock,
            $this->phoneOperatorGetterMock,
            $this->scoreCalculatingMock,
            $this->entityManagerMock
        );

        $this->initProfileData();
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws ValidationException
     * @throws PhoneOperatorException
     * @throws ClientEntityNotFoundException
     */
    public function testExecuteUpdateClientSuccessWithPhoneChange(): void
    {
        $clientId  = 3;
        $education = EducationEnum::Special;
        $consent   = false;

        $inputData = $this->createStub(ClientEntityUpdateDataInterface::class);
        $inputData->method('getId')->willReturn($clientId);
        $inputData->method('getEmail')->willReturn($this->profileEmail);
        $inputData->method('getPhone')->willReturn($this->phone);
        $inputData->method('getFirstName')->willReturn($this->firstName);
        $inputData->method('getLastName')->willReturn($this->lastName);
        $inputData->method('getEducation')->willReturn($education);
        $inputData->method('getConsentPersonalData')->willReturn($consent);

        $profileId   = 2;
        $profileMock = $this->createProfileMock($this, $profileId);
        $profileMock->method('getEmail')->willReturn($this->profileEmail);
        $profileMock->method('getFirstName')->willReturn($this->firstName);
        $profileMock->method('getLastName')->willReturn($this->lastName);

        // Сначала получаем другой телефон, чтобы сработало условие проверки на изменение оператора телефона.
        // Затем получаем обновленный
        $profileMock->expects($this->exactly(2))
            ->method('getPhone')
            ->willReturnOnConsecutiveCalls(new ProfilePhone('+79012345678'), $this->phone);

        $clientMock = $this->createClientMock($this, $clientId, $profileMock);
        $clientMock->method('getEducation')->willReturn($education);
        $clientMock->method('getConsentPersonalData')->willReturn($consent);

        $this->clientEntityGetterMock->expects($this->once())
            ->method('get')
            ->with($clientId)
            ->willReturn($clientMock);

        $validationErrors = $this->createStub(ValidationErrorCollection::class);
        $validationErrors->method('isValid')->willReturn(true);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($clientMock, $inputData)
            ->willReturn($validationErrors);

        $profileMock->expects($this->once())->method('setEmail')->with($this->profileEmail)->willReturnSelf();
        $profileMock->expects($this->once())->method('setPhone')->with($this->phone)->willReturnSelf();
        $profileMock->expects($this->once())->method('setFirstName')->with($this->firstName)->willReturnSelf();
        $profileMock->expects($this->once())->method('setLastName')->with($this->lastName)->willReturnSelf();

        $clientMock->expects($this->once())->method('setEducation')->with($education)->willReturnSelf();
        $clientMock->expects($this->once())->method('setConsentPersonalData')->with($consent)->willReturnSelf();

        $phoneOperator = PhoneOperator::fromPhoneOperator('operator');

        $this->phoneOperatorGetterMock->expects($this->once())
            ->method('get')
            ->with((string)$this->phone)
            ->willReturn($phoneOperator);

        $clientMock->expects($this->once())
            ->method('setPhoneOperator')
            ->with($phoneOperator)->willReturnSelf();

        $clientMock->method('getPhoneOperator')->willReturn($phoneOperator);

        $scoringResult = new ClientScoringData(123, new ScoringDataCollection());

        $this->scoreCalculatingMock->expects($this->once())
            ->method('calculate')
            ->with($clientMock)
            ->willReturn($scoringResult);

        $clientMock->expects($this->once())
            ->method('setScore')
            ->with($scoringResult->getScore())->willReturnSelf();

        $clientMock->method('getScore')->willReturn($scoringResult->getScore());

        $this->entityManagerMock->expects($this->once())->method('flush');

        $result = $this->command->execute($inputData);

        $this->assertSame($inputData->getEducation(), $result->getEducation());
        $this->assertSame($inputData->getConsentPersonalData(), $result->getConsentPersonalData());
        $this->assertSame((string)$inputData->getEmail(), $result->getProfile()->getEmail());
        $this->assertSame((string)$inputData->getPhone(), $result->getProfile()->getPhone());
        $this->assertSame((string)$inputData->getFirstName(), $result->getProfile()->getFirstName());
        $this->assertSame((string)$inputData->getLastName(), $result->getProfile()->getLastName());
        $this->assertSame((string)$phoneOperator, $result->getPhoneOperator());
        $this->assertSame($scoringResult->getScore(), $result->getScore());
    }

    /**
     * @throws ClientEntityNotFoundException
     * @throws Exception
     * @throws EntityManagerException
     * @throws ValidationException
     * @throws PhoneOperatorException
     */
    public function testExecuteUpdateClientSuccessWithoutPhoneChangeOperatorAlreadySet(): void
    {
        $clientId  = 3;
        $education = EducationEnum::Special;
        $consent   = false;

        $inputData = $this->createStub(ClientEntityUpdateDataInterface::class);
        $inputData->method('getId')->willReturn($clientId);
        $inputData->method('getEmail')->willReturn($this->profileEmail);
        $inputData->method('getPhone')->willReturn($this->phone);
        $inputData->method('getFirstName')->willReturn($this->firstName);
        $inputData->method('getLastName')->willReturn($this->lastName);
        $inputData->method('getEducation')->willReturn($education);
        $inputData->method('getConsentPersonalData')->willReturn($consent);

        $profileId   = 2;
        $profileMock = $this->createProfileMock($this, $profileId);
        $profileMock->method('getEmail')->willReturn($this->profileEmail);
        $profileMock->method('getFirstName')->willReturn($this->firstName);
        $profileMock->method('getLastName')->willReturn($this->lastName);
        $profileMock->method('getPhone')->willReturn($this->phone);

        $phoneOperator = PhoneOperator::fromPhoneOperator('operator');

        $clientMock = $this->createClientMock($this, $clientId, $profileMock);
        $clientMock->method('getEducation')->willReturn($education);
        $clientMock->method('getConsentPersonalData')->willReturn($consent);
        $clientMock->method('getPhoneOperator')->willReturn($phoneOperator);

        $this->clientEntityGetterMock->expects($this->once())
            ->method('get')
            ->with($clientId)
            ->willReturn($clientMock);

        $validationErrors = $this->createStub(ValidationErrorCollection::class);
        $validationErrors->method('isValid')->willReturn(true);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($clientMock, $inputData)
            ->willReturn($validationErrors);

        $profileMock->expects($this->once())->method('setEmail')->with($this->profileEmail)->willReturnSelf();
        $profileMock->expects($this->once())->method('setPhone')->with($this->phone)->willReturnSelf();
        $profileMock->expects($this->once())->method('setFirstName')->with($this->firstName)->willReturnSelf();
        $profileMock->expects($this->once())->method('setLastName')->with($this->lastName)->willReturnSelf();

        $clientMock->expects($this->once())->method('setEducation')->with($education)->willReturnSelf();
        $clientMock->expects($this->once())->method('setConsentPersonalData')->with($consent)->willReturnSelf();

        $this->phoneOperatorGetterMock->expects($this->never())->method('get');

        $clientMock->expects($this->never())->method('setPhoneOperator');

        $scoringResult = new ClientScoringData(123, new ScoringDataCollection());

        $this->scoreCalculatingMock->expects($this->once())
            ->method('calculate')
            ->with($clientMock)
            ->willReturn($scoringResult);

        $clientMock->expects($this->once())
            ->method('setScore')
            ->with($scoringResult->getScore())->willReturnSelf();

        $clientMock->method('getScore')->willReturn($scoringResult->getScore());

        $this->entityManagerMock->expects($this->once())->method('flush');

        $result = $this->command->execute($inputData);

        $this->assertSame($inputData->getEducation(), $result->getEducation());
        $this->assertSame($inputData->getConsentPersonalData(), $result->getConsentPersonalData());
        $this->assertSame((string)$inputData->getEmail(), $result->getProfile()->getEmail());
        $this->assertSame((string)$inputData->getPhone(), $result->getProfile()->getPhone());
        $this->assertSame((string)$inputData->getFirstName(), $result->getProfile()->getFirstName());
        $this->assertSame((string)$inputData->getLastName(), $result->getProfile()->getLastName());
        $this->assertSame((string)$phoneOperator, $result->getPhoneOperator());
        $this->assertSame($scoringResult->getScore(), $result->getScore());
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     * @throws ClientEntityNotFoundException
     */
    public function testExecuteThrowsValidationExceptionOnInvalidData(): void
    {
        $clientId = 1;

        $inputData = $this->createStub(ClientEntityUpdateDataInterface::class);
        $inputData->method('getId')->willReturn($clientId);

        $clientMock = $this->createClientMock($this, $clientId);
        $this->clientEntityGetterMock->expects($this->once())
            ->method('get')
            ->with($clientId)
            ->willReturn($clientMock);

        $validationErrors = $this->createStub(ValidationErrorCollection::class);
        $validationErrors->method('isValid')->willReturn(false);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($clientMock, $inputData)
            ->willReturn($validationErrors);

        $this->phoneOperatorGetterMock->expects($this->never())->method('get');
        $this->scoreCalculatingMock->expects($this->never())->method('calculate');
        $this->entityManagerMock->expects($this->never())->method('flush');

        $this->expectException(ValidationException::class);

        $this->command->execute($inputData);
    }
}
