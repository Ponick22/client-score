<?php

namespace App\Tests\Unit\Application\Client\ValueObject;

use App\Application\Client\DTO\ClientOutputData;
use App\Application\Client\ValueObject\ClientOutputDataCollection;
use PHPUnit\Framework\TestCase;

class ClientOutputDataCollectionTest extends TestCase
{
    public function testGetClassReturnsAndToArray(): void
    {
        $data1 = $this->createStub(ClientOutputData::class);
        $data2 = $this->createStub(ClientOutputData::class);

        $collection = new ClientOutputDataCollection([$data1]);
        $collection->add($data2);

        $this->assertSame(ClientOutputData::class, $collection->getClass());
        $this->assertSame([$data1, $data2], $collection->toArray());
    }
}
