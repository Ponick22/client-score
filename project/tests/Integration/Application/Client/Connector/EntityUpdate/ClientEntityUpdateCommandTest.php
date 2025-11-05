<?php

namespace App\Tests\Integration\Application\Client\Connector\EntityUpdate;

use App\Application\Client\Connector\Command\EntityUpdate\ClientEntityUpdateCommand;
use App\Application\Client\Connector\Command\EntityUpdate\Contract\ClientEntityUpdateDataInterface;
use App\Application\Client\Exception\ClientEntityNotFoundException;
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
use App\Infrastructure\Doctrine\Entity\Client;
use App\Infrastructure\Doctrine\Entity\Profile;
use App\Tests\Support\TransactionalKernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;

class ClientEntityUpdateCommandTest extends TransactionalKernelTestCase
{
    protected EntityManagerInterface  $entityManager;
    private ClientEntityUpdateCommand $command;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();

        $this->command = $container->get(ClientEntityUpdateCommand::class);
    }

    /**
     * @throws EntityManagerException
     * @throws Exception
     * @throws PhoneOperatorException
     * @throws ProfileEmailInvalidException
     * @throws ProfileFirstNameInvalidException
     * @throws ProfileLastNameInvalidException
     * @throws ProfilePhoneInvalidException
     * @throws ValidationException
     * @throws ClientEntityNotFoundException
     */
    #[DataProvider('updateClientSuccessProvider')]
    public function testExecuteUpdateClientSuccessfully(
        string        $email,
        string        $phone,
        EducationEnum $education,
        ?string       $firstName,
        ?string       $lastName,
        bool          $consentPersonalData,
    ): void
    {
        $profileEmail = new ProfileEmail($email);
        $phone        = new ProfilePhone($phone);
        $firstName    = $firstName ? new ProfileFirstName($firstName) : null;
        $lastName     = $lastName ? new ProfileLastName($lastName) : null;

        // user1@example.com - e-mail из фикстуры,
        /** @var Client $client */
        $client              = $this->getClientByEmail(new ProfileEmail('user1@example.com'));
        $oldClientUpdatedAt  = $client->getUpdatedAt();
        $oldProfileUpdatedAt = $client->getProfile()->getUpdatedAt();

        $updateData = $this->createStub(ClientEntityUpdateDataInterface::class);
        $updateData->method('getId')->willReturn($client->getId());
        $updateData->method('getEmail')->willReturn($profileEmail);
        $updateData->method('getPhone')->willReturn($phone);
        $updateData->method('getEducation')->willReturn($education);
        $updateData->method('getFirstName')->willReturn($firstName);
        $updateData->method('getLastName')->willReturn($lastName);
        $updateData->method('getConsentPersonalData')->willReturn($consentPersonalData);

        $outputData  = $this->command->execute($updateData);
        $profileData = $outputData->getProfile();

        $this->assertSame($education, $outputData->getEducation());
        $this->assertSame($consentPersonalData, $outputData->getConsentPersonalData());
        $this->assertSame((string)$profileEmail, $profileData->getEmail());
        $this->assertSame((string)$phone, $profileData->getPhone());
        $this->assertSame(($firstName ? (string)$firstName : null), $profileData->getFirstName());
        $this->assertSame(($lastName ? (string)$lastName : null), $profileData->getLastName());
        $this->assertTrue($oldClientUpdatedAt->getTimestamp() <= $outputData->getUpdatedAt()->getTimestamp());
        $this->assertTrue($oldProfileUpdatedAt->getTimestamp() <= $profileData->getUpdatedAt()->getTimestamp());
    }

    public static function updateClientSuccessProvider(): array
    {
        return [
            'full'            => [
                'user10@example.com',
                '8 (955) 123-45-67',
                EducationEnum::Higher,
                'Иван',
                'Иванов',
                false,
            ],
            'only email'      => [
                'user10@example.com',
                '79123456789',
                EducationEnum::Special,
                'John1',
                'Doe1',
                true,
            ],
            'only phone'      => [
                'user1@example.com',
                '8 (955) 123-45-67',
                EducationEnum::Special,
                'John1',
                'Doe1',
                true,
            ],
            'only education'  => [
                'user1@example.com',
                '79123456789',
                EducationEnum::Higher,
                'John1',
                'Doe1',
                true,
            ],
            'only first name' => [
                'user1@example.com',
                '79123456789',
                EducationEnum::Special,
                'Иван',
                'Doe1',
                true,
            ],
            'only last name'  => [
                'user1@example.com',
                '79123456789',
                EducationEnum::Special,
                'John1',
                'Иванов',
                true,
            ],
            'only consent'    => [
                'user1@example.com',
                '79123456789',
                EducationEnum::Special,
                'John1',
                'Doe1',
                false,
            ],
            'no changes'      => [
                'user1@example.com',
                '79123456789',
                EducationEnum::Special,
                'John1',
                'Doe1',
                true,
            ],
        ];
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     * @throws ClientEntityNotFoundException
     */
    public function testExecuteThrowsValidationExceptionWhenProfileEmailAlreadyExists(): void
    {
        // user1@example.com - e-mail из фикстуры
        $client = $this->getClientByEmail(new ProfileEmail('user1@example.com'));

        // user2@example.com - e-mail существующий в фикстуре
        $profileEmail = new ProfileEmail('user2@example.com');

        $updateData = $this->createStub(ClientEntityUpdateDataInterface::class);
        $updateData->method('getId')->willReturn($client->getId());
        $updateData->method('getEmail')->willReturn($profileEmail);

        try {
            $this->command->execute($updateData);

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
     * @throws ClientEntityNotFoundException
     */
    public function testExecuteThrowsValidationExceptionWhenPhoneAlreadyExists(): void
    {
        // user1@example.com - e-mail из фикстуры
        $client = $this->getClientByEmail(new ProfileEmail('user1@example.com'));

        // 89223456789 - телефон существующий в фикстуре
        $phone = new ProfilePhone('89223456789');

        $updateData = $this->createStub(ClientEntityUpdateDataInterface::class);
        $updateData->method('getId')->willReturn($client->getId());
        $updateData->method('getPhone')->willReturn($phone);

        try {
            $this->command->execute($updateData);

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

    private function getClientByEmail(ProfileEmail $email): ?Client
    {
        return $this->entityManager->getRepository(Client::class)->createQueryBuilder('c')
            ->join('c.profile', 'p')
            ->andWhere('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
