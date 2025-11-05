<?php

namespace App\Tests\Unit\Application\Scoring\Types;

use App\Application\Scoring\Types\PhoneOperatorScoring;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PhoneOperatorScoringTest extends TestCase
{
    #[DataProvider('scoringProvider')]
    public function testScoring(array $rules, string $operator, int $expectedScore): void
    {
        $scorer = new PhoneOperatorScoring($rules);

        $result = $scorer->scoring($operator);

        $this->assertSame($expectedScore, $result->getScore());
        $this->assertSame($operator, $result->getValue());
        $this->assertSame('phone_operator', $result->getName());
    }

    public static function scoringProvider(): array
    {
        $rules = [
            'phone_operator' => [
                'default' => -1,
                'list'    => [
                    'билайн' => 15,
                    'мтс'    => 10,
                ],
            ],
        ];

        return [
            [
                $rules,
                'мтс',
                10,
            ],
            [
                $rules,
                'мегафон',
                -1,
            ],
            [
                $rules,
                '',
                -1,
            ],
            [
                [],
                'билайн',
                0,
            ],
        ];
    }
}
