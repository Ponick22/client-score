<?php

namespace App\Domain\User\ValueObject;

use App\Domain\User\Enum\UserRoleEnum;
use App\Util\Collection\ArrayCollectionAbstract;

/**
 * @extends ArrayCollectionAbstract<UserRoleEnum>
 * @method UserRoleEnum offsetGet()
 * @method add(UserRoleEnum $value)
 */
class UserRoleEnumCollection extends ArrayCollectionAbstract
{
    public function getClass(): string
    {
        return UserRoleEnum::class;
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        return array_map(fn(UserRoleEnum $role) => $role->value, $array);
    }
}
