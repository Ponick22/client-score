<?php

namespace App\Domain\User\Enum;

enum UserRoleEnum: string
{
    case Client = 'client';
    case Admin  = 'admin';
}
