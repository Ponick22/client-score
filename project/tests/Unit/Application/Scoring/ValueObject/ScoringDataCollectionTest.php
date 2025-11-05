<?php

namespace App\Tests\Unit\Application\Scoring\ValueObject;

use App\Application\Scoring\DTO\ScoringData;
use App\Application\Scoring\ValueObject\ScoringDataCollection;
use PHPUnit\Framework\TestCase;

class ScoringDataCollectionTest extends TestCase
{
    public function testGetClassReturnsAndToArray(): void
    {
        $data1 = new ScoringData(10, 'example.com', 'email_domain');
        $data2 = new ScoringData(5, 'beeline', 'phone_operator');

        $collection = new ScoringDataCollection([$data1]);
        $collection->add($data2);

        $this->assertSame(ScoringData::class, $collection->getClass());
        $this->assertSame([$data1, $data2], $collection->toArray());
    }
}
