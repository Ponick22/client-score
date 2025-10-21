<?php

namespace App\Domain\Client\Enum;

enum EducationEnum: string
{
    case Secondary = 'secondary';
    case Special   = 'special';
    case Higher    = 'higher';
}
