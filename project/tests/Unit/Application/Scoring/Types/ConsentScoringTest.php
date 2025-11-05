<?php

namespace App\Tests\Unit\Application\Scoring\Types;

use App\Application\Scoring\Types\ConsentScoring;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConsentScoringTest extends TestCase
{
    #[DataProvider('scoringProvider')]
    public function testScoring(array $rules, string $consent, string $expectedConsent, int $expectedScore): void
    {
        $scorer = new ConsentScoring($rules);

        $result = $scorer->scoring($consent);

        $this->assertSame($expectedScore, $result->getScore());
        $this->assertSame($expectedConsent, $result->getValue());
        $this->assertSame('consent', $result->getName());
    }

    public static function scoringProvider(): array
    {
        $rules = [
            'consent' => [
                'default' => -1,
                'list'    => [
                    'true' => 7,
                ],
            ],
        ];

        return [
            [
                $rules,
                '1',
                'true',
                7,
            ],
            [
                $rules,
                '0',
                'false',
                -1,
            ],
            [
                [],
                '1',
                'true',
                0,
            ],
        ];
    }
}
