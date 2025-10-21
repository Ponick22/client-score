<?php

namespace App\Domain\Client\ValueObject;

use App\Domain\Client\Entity\ClientEntityInterface;
use App\Util\Collection\ArrayCollectionAbstract;

/**
 * @extends ArrayCollectionAbstract<ClientEntityInterface>
 * @method ClientEntityInterface offsetGet()
 * @method add(ClientEntityInterface $value)
 */
abstract class ClientEntityCollectionAbstract extends ArrayCollectionAbstract
{
    public function getClass(): string
    {
        return ClientEntityInterface::class;
    }
}
