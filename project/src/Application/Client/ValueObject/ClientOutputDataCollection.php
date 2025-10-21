<?php

namespace App\Application\Client\ValueObject;

use App\Application\Client\DTO\ClientOutputData;
use App\Util\Collection\ArrayCollectionAbstract;

/**
 * @extends ArrayCollectionAbstract<ClientOutputData>
 * @method ClientOutputData offsetGet()
 * @method add(ClientOutputData $value)
 */
class ClientOutputDataCollection extends ArrayCollectionAbstract
{
    public function getClass(): string
    {
        return ClientOutputData::class;
    }
}
