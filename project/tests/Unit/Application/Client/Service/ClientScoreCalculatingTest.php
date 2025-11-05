<?php

namespace App\Tests\Unit\Application\Client\Service;

use App\Application\Client\Service\ClientScoreCalculating;
use App\Application\Scoring\DTO\ScoringData;
use App\Application\Scoring\Types\ConsentScoring;
use App\Application\Scoring\Types\EducationScoring;
use App\Application\Scoring\Types\EmailDomainScoring;
use App\Application\Scoring\Types\PhoneOperatorScoring;
use App\Domain\Client\Enum\EducationEnum;
use App\Domain\Profile\ValueObject\ProfileEmail;
use App\Tests\Traits\ClientMocksTrait;
use PHPUnit\Framework\TestCase;

class ClientScoreCalculatingTest extends TestCase
{
    use ClientMocksTrait;

    private ConsentScoring       $consentScoringMock;
    private EducationScoring     $educationScoringMock;
    private EmailDomainScoring   $emailDomainScoringMock;
    private PhoneOperatorScoring $phoneOperatorScoringMock;

    private ClientScoreCalculating $clientScoreCalculating;

    protected function setUp(): void
    {
        $this->consentScoringMock       = $this->createMock(ConsentScoring::class);
        $this->educationScoringMock     = $this->createMock(EducationScoring::class);
        $this->emailDomainScoringMock   = $this->createMock(EmailDomainScoring::class);
        $this->phoneOperatorScoringMock = $this->createMock(PhoneOperatorScoring::class);

        $this->clientScoreCalculating = new ClientScoreCalculating([
            $this->consentScoringMock,
            $this->educationScoringMock,
            $this->emailDomainScoringMock,
            $this->phoneOperatorScoringMock,
        ]);
    }

    public function testCalculateScoreAndSkipsNullValues(): void
    {
        $consent   = true;
        $education = EducationEnum::Higher;
        $email     = new ProfileEmail('profile@gmail.com');

        $profileMock = $this->createProfileMock($this);
        $profileMock->method('getEmail')->willReturn($email);

        $clientMock = $this->createClientMock($this, profile: $profileMock);
        $clientMock->method('getConsentPersonalData')->willReturn($consent);
        $clientMock->method('getEducation')->willReturn($education);
        $clientMock->method('getPhoneOperator')->willReturn(null);

        // Скоринг вызывается для 3 правил, телефонный оператор пропускается (value === null)
        $this->consentScoringMock->expects($this->once())
            ->method('scoring')
            ->with($consent)
            ->willReturn(new ScoringData(10, $consent, 'consent'));

        $this->educationScoringMock->expects($this->once())
            ->method('scoring')
            ->with($education->value)
            ->willReturn(new ScoringData(20, $education->value, 'education'));

        $this->emailDomainScoringMock->expects($this->once())
            ->method('scoring')
            ->with((string)$email)
            ->willReturn(new ScoringData(30, (string)$email, 'email'));

        $this->phoneOperatorScoringMock->expects($this->never())->method('scoring');

        $result = $this->clientScoreCalculating->calculate($clientMock);

        $this->assertSame(60, $result->getScore());
        $details = $result->getScoreDetails();

        $this->assertCount(3, $details);
    }
}
