<?php

namespace App\Domain\Profile\ValueObject;

use App\Domain\Profile\Entity\ProfileEntityInterface;
use App\Util\Collection\ArrayCollectionAbstract;

/**
 * @extends ArrayCollectionAbstract<ProfileEntityInterface>
 * @method ProfileEntityInterface offsetGet()
 * @method add(ProfileEntityInterface $value)
 */
abstract class ProfileEntityCollectionAbstract extends ArrayCollectionAbstract
{
    public function getClass(): string
    {
        return ProfileEntityInterface::class;
    }
}
