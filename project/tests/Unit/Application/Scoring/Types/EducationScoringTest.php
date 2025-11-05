<?php

namespace App\Tests\Unit\Application\Scoring\Types;

use App\Application\Scoring\Types\EducationScoring;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EducationScoringTest extends TestCase
{
    #[DataProvider('scoringProvider')]
    public function testScoring(array $rules, string $education, int $expectedScore): void
    {
        $scorer = new EducationScoring($rules);

        $result = $scorer->scoring($education);

        $this->assertSame($expectedScore, $result->getScore());
        $this->assertSame($education, $result->getValue());
        $this->assertSame('education', $result->getName());
    }

    public static function scoringProvider(): array
    {
        $rules = [
            'education' => [
                'default' => -1,
                'list'    => [
                    'high'      => 10,
                    'secondary' => 4,
                ],
            ],
        ];

        return [
            [
                $rules,
                'high',
                10,
            ],
            [
                $rules,
                'special',
                -1,
            ],
            [
                [],
                'secondary',
                0,
            ],
        ];
    }
}
