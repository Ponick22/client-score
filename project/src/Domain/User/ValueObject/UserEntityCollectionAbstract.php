<?php

namespace App\Domain\User\ValueObject;

use App\Domain\User\Entity\UserEntityInterface;
use App\Util\Collection\ArrayCollectionAbstract;

/**
 * @extends ArrayCollectionAbstract<UserEntityInterface>
 * @method UserEntityInterface offsetGet()
 * @method add(UserEntityInterface $value)
 */
abstract class UserEntityCollectionAbstract extends ArrayCollectionAbstract
{
    public function getClass(): string
    {
        return UserEntityInterface::class;
    }
}
