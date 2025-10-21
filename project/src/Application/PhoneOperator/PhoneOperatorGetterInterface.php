<?php

namespace App\Application\PhoneOperator;

use App\Application\PhoneOperator\Exception\PhoneOperatorException;

interface PhoneOperatorGetterInterface
{
    /**
     * @throws PhoneOperatorException
     */
    public function get(string $phone): ?string;
}
