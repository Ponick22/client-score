<?php

namespace App\Tests\Integration\Application\Client\Connector\EntityCreation;

use App\Application\Client\Connector\Command\EntityCreation\ClientEntityCreationCommand;
use App\Application\Client\Connector\Command\EntityCreation\Contract\ClientEntityCreationDataInterface;
use App\Application\Exception\ValidationException;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\Profile\Exception\ProfileEmailInvalidException;
use App\Domain\Profile\Exception\ProfileFirstNameInvalidException;
use App\Domain\Profile\Exception\ProfileLastNameInvalidException;
use App\Domain\Profile\Exception\ProfilePhoneInvalidException;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Domain\Profile\ValueObject\ProfileFirstName;
use App\Domain\Profile\ValueObject\ProfileLastName;
use App\Domain\Profile\ValueObject\ProfilePhone;
use App\Domain\User\Enum\UserRoleEnum;
use App\Domain\User\Exception\UserEmailInvalidException;
use App\Domain\User\ValueObject\UserEmail;
use App\Infrastructure\Doctrine\Entity\Client;
use App\Infrastructure\Doctrine\Entity\Profile;
use App\Infrastructure\Doctrine\Entity\User;
use App\Tests\Support\TransactionalKernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;

class ClientEntityCreationCommandTest extends TransactionalKernelTestCase
{
    protected EntityManagerInterface    $entityManager;
    private ClientEntityCreationCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();

        $this->command = $container->get(ClientEntityCreationCommand::class);
    }

    /**
     * @throws EntityManagerException
     * @throws Exception
     * @throws PhoneOperatorException
     * @throws ProfileEmailInvalidException
     * @throws ProfileFirstNameInvalidException
     * @throws ProfileLastNameInvalidException
     * @throws ProfilePhoneInvalidException
     * @throws UserEmailInvalidException
     * @throws ValidationException
     */
    #[DataProvider('createClientSuccessProvider')]
    public function testExecuteCreateClientSuccessfully(
        string        $email,
        string        $phone,
        EducationEnum $education,
        ?string       $firstName,
        ?string       $lastName,
        bool          $consentPersonalData,
    ): void
    {
        $userEmail    = new UserEmail($email);
        $profileEmail = new ProfileEmail($email);
        $phone        = new ProfilePhone($phone);
        $firstName    = $firstName ? new ProfileFirstName($firstName) : null;
        $lastName     = $lastName ? new ProfileLastName($lastName) : null;

        $createData = $this->createStub(ClientEntityCreationDataInterface::class);
        $createData->method('getUserEmail')->willReturn($userEmail);
        $createData->method('getProfileEmail')->willReturn($profileEmail);
        $createData->method('getPhone')->willReturn($phone);
        $createData->method('getEducation')->willReturn($education);
        $createData->method('getFirstName')->willReturn($firstName);
        $createData->method('getLastName')->willReturn($lastName);
        $createData->method('getConsentPersonalData')->willReturn($consentPersonalData);

        $outputData  = $this->command->execute($createData);
        $profileData = $outputData->getProfile();

        $this->assertSame($education, $outputData->getEducation());
        $this->assertSame($consentPersonalData, $outputData->getConsentPersonalData());
        $this->assertSame((string)$profileEmail, $profileData->getEmail());
        $this->assertSame((string)$phone, $profileData->getPhone());
        $this->assertSame(($firstName ? (string)$firstName : null), $profileData->getFirstName());
        $this->assertSame(($lastName ? (string)$lastName : null), $profileData->getLastName());
        $this->assertSame($outputData->getCreatedAt()->getTimestamp(), $outputData->getUpdatedAt()->getTimestamp());
        $this->assertSame($profileData->getCreatedAt()->getTimestamp(), $profileData->getUpdatedAt()->getTimestamp());

        /** @var Client $client */
        $client = $this->entityManager->getRepository(Client::class)->find($outputData->getId());
        $this->assertNotNull($client);

        $profile = $client->getProfile();
        $user    = $profile->getUser();

        $this->assertSame((string)$userEmail, (string)$user->getEmail());
        $this->assertContains(UserRoleEnum::Client->value, $user->getRoles()->toArray());
        $this->assertSame($profile->getCreatedAt()->getTimestamp(), $profile->getUpdatedAt()->getTimestamp());
    }

    public static function createClientSuccessProvider(): array
    {
        return [
            'full'          => [
                'user10@example.com',
                '8 (955) 123-45-67',
                EducationEnum::Higher,
                'Иван',
                'Иванов',
                true,
            ],
            'no last name'  => [
                'user11@example.com',
                '78325642365',
                EducationEnum::Special,
                'Петр',
                null,
                false,
            ],
            'no first name' => [
                'user12@example.com',
                '+79356417534',
                EducationEnum::Secondary,
                null,
                'Сидоров',
                true,
            ],
            'no names'      => [
                'user13@example.com',
                '89987654321',
                EducationEnum::Higher,
                null,
                null,
                false,
            ],
        ];
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     */
    public function testExecuteThrowsValidationExceptionWhenUserEmailAlreadyExists(): void
    {
        // user1@example.com - e-mail из фикстуры
        $userEmail = new UserEmail('user1@example.com');

        $createData = $this->createStub(ClientEntityCreationDataInterface::class);
        $createData->method('getUserEmail')->willReturn($userEmail);

        try {
            $this->command->execute($createData);

            $this->fail('Expected ValidationException for duplicate user email');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();

            $this->assertCount(1, $errors);
            $this->assertSame('email', $errors[0]->getProperty());
            $this->assertSame('error.user.email_already_exists', $errors[0]->getMessage());
        } finally {
            $count = $this->entityManager->getRepository(User::class)->count(['email' => $userEmail]);
            $this->assertSame(1, $count);
        }
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     */
    public function testExecuteThrowsValidationExceptionWhenProfileEmailAlreadyExists(): void
    {
        // user1@example.com - e-mail из фикстуры
        $profileEmail = new ProfileEmail('user1@example.com');

        $createData = $this->createStub(ClientEntityCreationDataInterface::class);
        $createData->method('getProfileEmail')->willReturn($profileEmail);

        try {
            $this->command->execute($createData);

            $this->fail('Expected ValidationException for duplicate profile email');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();

            $this->assertCount(1, $errors);
            $this->assertSame('email', $errors[0]->getProperty());
            $this->assertSame('error.profile.email_already_exists', $errors[0]->getMessage());
        } finally {
            $count = $this->entityManager->getRepository(Profile::class)->count(['email' => $profileEmail]);
            $this->assertSame(1, $count);
        }
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     */
    public function testExecuteThrowsValidationExceptionWhenPhoneAlreadyExists(): void
    {
        // 89123456789 - телефон существующий в фикстуре
        $phone = new ProfilePhone('89123456789');

        $createData = $this->createStub(ClientEntityCreationDataInterface::class);
        $createData->method('getPhone')->willReturn($phone);

        try {
            $this->command->execute($createData);

            $this->fail('Expected ValidationException for duplicate profile phone');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();

            $this->assertCount(1, $errors);
            $this->assertSame('phone', $errors[0]->getProperty());
            $this->assertSame('error.phone.already_exists', $errors[0]->getMessage());
        } finally {
            $count = $this->entityManager->getRepository(Profile::class)->count(['phone' => $phone]);
            $this->assertSame(1, $count);
        }
    }
}
