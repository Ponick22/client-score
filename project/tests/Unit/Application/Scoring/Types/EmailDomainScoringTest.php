<?php

namespace App\Tests\Unit\Application\Scoring\Types;

use App\Application\Scoring\Types\EmailDomainScoring;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmailDomainScoringTest extends TestCase
{
    #[DataProvider('scoringProvider')]
    public function testScoring(array $rules, string $email, string $expectedDomain, int $expectedScore): void
    {
        $scorer = new EmailDomainScoring($rules);

        $result = $scorer->scoring($email);

        $this->assertSame($expectedScore, $result->getScore());
        $this->assertSame($expectedDomain, $result->getValue());
        $this->assertSame('email_domain', $result->getName());
    }

    public static function scoringProvider(): array
    {
        $rules = [
            'email_domain' => [
                'default' => -1,
                'list'    => [
                    'example.com' => 5,
                    'gmail.com'   => 3,
                ],
            ],
        ];

        return [
            [
                $rules,
                'user@example.com',
                'example.com',
                5,
            ],
            [
                $rules,
                'user123@mail.com',
                'mail.com',
                -1,
            ],
            [
                [],
                'user@gmail.com',
                'gmail.com',
                0,
            ],
        ];
    }
}
