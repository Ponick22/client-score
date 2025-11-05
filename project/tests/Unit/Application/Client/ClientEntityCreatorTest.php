<?php

namespace App\Tests\Unit\Application\Client;

use App\Application\Client\ClientEntityCreator;
use App\Application\Client\DTO\ClientCreateData;
use App\Application\Client\DTO\ClientScoringData;
use App\Application\Client\Service\ClientScoreCalculating;
use App\Application\Scoring\ValueObject\ScoringDataCollection;
use App\Domain\Client\Entity\ClientEntityInterface;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Client\Factory\ClientEntityFactoryInterface;
use App\Domain\EntityManager\EntityManagerInterface;
use App\Domain\EntityManager\Exception\EntityManagerException;
use App\Domain\PhoneOperator\Exception\PhoneOperatorException;
use App\Domain\PhoneOperator\PhoneOperatorGetterInterface;
use App\Domain\PhoneOperator\ValueObject\PhoneOperator;
use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Domain\Profile\ValueObject\ProfilePhone;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientEntityCreatorTest extends KernelTestCase
{
    private ClientEntityFactoryInterface $factoryMock;
    private PhoneOperatorGetterInterface $operatorGetterMock;
    private ClientScoreCalculating       $scoreCalculatingMock;
    private EntityManagerInterface       $entityManagerMock;
    private ClientEntityCreator          $creator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->factoryMock          = $this->createMock(ClientEntityFactoryInterface::class);
        $this->operatorGetterMock   = $this->createMock(PhoneOperatorGetterInterface::class);
        $this->scoreCalculatingMock = $this->createMock(ClientScoreCalculating::class);
        $this->entityManagerMock    = $this->createMock(EntityManagerInterface::class);

        $this->creator = new ClientEntityCreator(
            $this->factoryMock,
            $this->operatorGetterMock,
            $this->scoreCalculatingMock,
            $this->entityManagerMock
        );
    }

    /**
     * @throws Exception
     * @throws EntityManagerException
     * @throws PhoneOperatorException
     */
    public function testCreateSuccess(): void
    {
        $profileMock = $this->createMock(ProfileEntityInterface::class);
        $profileMock->method('getPhone')->willReturn(new ProfilePhone('89123456789'));

        $education     = EducationEnum::Higher;
        $consent       = false;
        $phoneOperator = PhoneOperator::fromPhoneOperator('оператор');


        $createData = new ClientCreateData(
            $profileMock,
            $education,
            $consent,
        );

        $entityMock = $this->createMock(ClientEntityInterface::class);
        $this->factoryMock->expects($this->once())->method('create')->willReturn($entityMock);

        $entityMock->expects($this->once())
            ->method('setProfile')
            ->with($profileMock)
            ->willReturnSelf();
        $entityMock->expects($this->once())
            ->method('setEducation')
            ->with($education)
            ->willReturnSelf();
        $entityMock->expects($this->once())
            ->method('setConsentPersonalData')
            ->with($consent)
            ->willReturnSelf();

        $this->operatorGetterMock->expects($this->once())
            ->method('get')
            ->with((string)$profileMock->getPhone())
            ->willReturn($phoneOperator);

        $entityMock->expects($this->once())
            ->method('setPhoneOperator')
            ->with($phoneOperator)
            ->willReturnSelf();

        $scoringResult = new ClientScoringData(123, new ScoringDataCollection());

        $this->scoreCalculatingMock->expects($this->once())
            ->method('calculate')
            ->with($entityMock)
            ->willReturn($scoringResult);

        $entityMock->expects($this->once())
            ->method('setScore')
            ->with($scoringResult->getScore())
            ->willReturnSelf();

        $this->entityManagerMock->expects($this->once())->method('persist')->with($entityMock);

        $result = $this->creator->create($createData);

        $this->assertSame($entityMock, $result);
    }
}
