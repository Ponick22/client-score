<?php

namespace App\Application\Scoring\ValueObject;

use App\Application\Scoring\DTO\ScoringData;
use App\Util\Collection\ArrayCollectionAbstract;

/**
 * @extends ArrayCollectionAbstract<ScoringData>
 * @method ScoringData offsetGet()
 * @method add(ScoringData $value)
 */
class ScoringDataCollection extends ArrayCollectionAbstract
{
    public function getClass(): string
    {
        return ScoringData::class;
    }
}
